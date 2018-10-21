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

if (!is_file("control-files/$requestParser->location.txt")) 
{
	touch("control-files/$requestParser->location.txt");
}

$locationTriggersEnabled = true;
$firstPersonArrived = false;
$lastPersonDeparted = false;

$allPeople = explode(',', file_get_contents('control-files/people.txt'));
if (!in_array($requestParser->person, $allPeople)) 
{
    throw new Exception("Person {$requestParser->person} not found");
}

$locationPeople = trim(file_get_contents("control-files/$requestParser->location.txt")) != '' ? explode(',', file_get_contents("control-files/$requestParser->location.txt")) : array();

if ($requestParser->status == 'arrived' && !in_array($requestParser->person, $locationPeople))
{
    if (empty($locationPeople)) $firstPersonArrived = true;

    $locationPeople[] = $requestParser->person;
    file_put_contents("control-files/$requestParser->location.txt", implode(',', $locationPeople));
}
else if ($requestParser->status == 'departed' && in_array($requestParser->person, $locationPeople))
{
    unset($locationPeople[array_search($requestParser->person, $locationPeople, true)]);
    file_put_contents("control-files/$requestParser->location.txt", implode(',', $locationPeople));

    if (empty($locationPeople)) $lastPersonDeparted = true;
}

if ($locationTriggersEnabled)
{
    if ($requestParser->location == 'home' && $firstPersonArrived) 
    {
        $dayOrNight = $nature->dayOrNight();
        if ($dayOrNight == 'night')
        {
            $smartThings->turnLightOn('Tray Light');
        }
    }

    if ($requestParser->location == 'home' && $lastPersonDeparted)
    {
        $smartThings->turnLightOff('Tray Light');
        $htd->turnOffAllSpeakers();
        $myQ->closeGarageDoor('Main Door');
    }
}
?>