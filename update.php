<?php
require_once dirname(__FILE__).'/classes/authentication.php';
$authentication = new Authentication();

require_once dirname(__FILE__).'/classes/request-parser.php';
$requestParser = new RequestParser();

$jsonBody = $authentication->getRequestJSON(file_get_contents('php://input'));
$authentication->authenticateRequest($jsonBody);

$requestParser->requestBody = $jsonBody;
$requestParser->parseRequest();

require_once dirname(__FILE__).'/classes/nature.php';
$nature = new Nature();

require_once dirname(__FILE__).'/classes/htd.php';
$htd = new HTD();

require_once dirname(__FILE__).'/classes/myq.php';
$myQ = new MyQ();

require_once dirname(__FILE__).'/classes/smartthings.php';
$smartThings = new SmartThings();

require_once dirname(__FILE__).'/classes/nest.php';
$nest = new Nest();

require_once dirname(__FILE__).'/classes/pushbullet.php';
$pushbullet = new Pushbullet();

if (!is_file("control-files/$requestParser->location.json")) 
{
	touch("control-files/$requestParser->location.json");
}

$locationTriggersEnabled = true;
$firstPersonArrived = false;
$lastPersonDeparted = false;

$allPeople = json_decode(file_get_contents('control-files/people.json'), true);
if (!in_array($requestParser->person, $allPeople)) 
{
    throw new Exception("Person {$requestParser->person} not found");
}

$locationPeople = json_decode(file_get_contents("control-files/$requestParser->location.json"), true);

if ($requestParser->status == 'arrived' && !in_array($requestParser->person, $locationPeople))
{
    if (empty($locationPeople)) $firstPersonArrived = true;

    $locationPeople[] = $requestParser->person;
    file_put_contents("control-files/$requestParser->location.json", json_encode($locationPeople));
}
else if ($requestParser->status == 'departed' && in_array($requestParser->person, $locationPeople))
{
    unset($locationPeople[array_search($requestParser->person, $locationPeople, true)]);
    file_put_contents("control-files/$requestParser->location.json", json_encode($locationPeople));

    if (empty($locationPeople)) $lastPersonDeparted = true;
}

if ($locationTriggersEnabled)
{
    if ($requestParser->location == 'home' && $requestParser->status == 'arrived') {
        $currentTemperature = $nest->getTemperature();
        $currentHumidity = $nest->getHumidity();
        if ($currentHumidity < 35 || $currentHumidity > 50) {
            $deviceName = ucwords(strtolower($requestParser->person))." - Phone";
            $noteBody = "Temperature: ".round($currentTemperature, 1)."° Humidity: $currentHumidity%";
            $pushbullet->pushNote($deviceName, "Humidity Alert", $noteBody);
        }
    }

    if ($requestParser->location == 'home' && $firstPersonArrived)
    {
        $dayOrNight = $nature->dayOrNight();
        if ($dayOrNight == 'night')
        {
            $smartThings->setSwitchPowerState('Kitchen Main Light', true);
        }
    }

    if ($requestParser->location == 'home' && $lastPersonDeparted)
    {
        $smartThings->setAllSwitchesPowerState(false);
        $htd->turnOffAllSpeakers();
        $myQ->closeGarageDoor('Main Door');
    }
}
?>