<?php
if (strcasecmp(filter_input(INPUT_SERVER, 'REQUEST_METHOD'), 'POST') != 0)
{
    throw new Exception('Request method must be POST');
}

$contentType = filter_input(INPUT_SERVER, 'CONTENT_TYPE') !== null ? trim(filter_input(INPUT_SERVER, 'CONTENT_TYPE')) : '';
if (strcasecmp($contentType, 'application/json') != 0)
{
    throw new Exception('Content type must be: application/json');
}

$jsonBody = json_decode(file_get_contents('php://input'), true);

if (!is_array($jsonBody))
{
    throw new Exception('Received content contained invalid JSON');
}

if ($jsonBody['authCode'] != getenv('HTTPS_AUTHENTICATION_SECRET')) 
{
    throw new Exception('Auth Code is invalid');
}

$location = $jsonBody['location'];
$person = $jsonBody['person'];
$status = $jsonBody['status'];

if (!is_file("control-files/$location.txt")) 
{
	touch("control-files/$location.txt");
}

$location_triggers_enabled = true;

$all_people = explode(',', file_get_contents('control-files/people.txt'));
if (!in_array($person, $all_people)) 
{
    throw new Exception("Person {$person} not found");
}

$location_people = trim(file_get_contents("control-files/$location.txt")) != '' ? explode(',', file_get_contents("control-files/$location.txt")) : array();

$first_person_arrived = $location_people == array() ? true : false;

// Update Person List For Location
if ($status == 'arrived' && !in_array($person, $location_people))
{
    $location_people[] = $person;
    file_put_contents("control-files/$location.txt", implode(',', $location_people));
}
else if ($status == 'departed' && in_array($person, $location_people))
{
    unset($location_people[array_search($person, $location_people, true)]);
    file_put_contents("control-files/$location.txt", implode(',', $location_people));
}

include_once('../nature/get_sunrise_sunset_times.php');

// First Person To Arrive Home
if ($location_triggers_enabled && $location == 'home' && $status == 'arrived' && $first_person_arrived) 
{
    if ($day_night == 'day') 
    {
        turnOnUpstairsSpeakers();
    }

    if ($day_night == 'night') 
    {
        turnOnTrayLight();
    }
}

// Last Person To Depart Home
if ($location_triggers_enabled && $location == 'home' && $status == 'departed' && $location_people == array()) 
{
    turnOffTrayLight();
    turnOffAllSpeakers();
}

function turnOffAllSpeakers() 
{
    $url = 'https://lounsbrough.ddns.net/htd/api/control.php';
    $postData = array(
        'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
        'command'=>'powerOff'
    );
    $postJSON = json_encode($postData);
    postJSONRequest($url, $postJSON);
}

function turnOnUpstairsSpeakers()
{
    $url = 'https://lounsbrough.ddns.net/htd/api/control.php';
    $postData = array(
        'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
        'command'=>'powerOn',
        'zones'=>array(
            array('name'=>'Great Room'),
            array('name'=>'Master Bedroom'),
            array('name'=>'Office')
        )
    );
    $postJSON = json_encode($postData);
    echo $postJSON;
    postJSONRequest($url, $postJSON);
}

function turnOnTrayLight()
{
    $url = 'https://lounsbrough.ddns.net/smartthings';
    $postData = array(
        'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
        'deviceName'=>'Tray Light',
        'action'=>'turnLightOn'
    );
    $postJSON = json_encode($postData);
    echo $postJSON;
    postJSONRequest($url, $postJSON);
}

function turnOffTrayLight()
{
    $url = 'https://lounsbrough.ddns.net/smartthings';
    $postData = array(
        'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
        'deviceName'=>'Tray Light',
        'action'=>'turnLightOff'
    );
    $postJSON = json_encode($postData);
    echo $postJSON;
    postJSONRequest($url, $postJSON);
}

function postJSONRequest($url, $json)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json))
    );

    return curl_exec($ch);
}
?>