<?php
require_once dirname(__FILE__).'/classes/authentication.php';
$authentication = new Authentication();

$jsonBody = $authentication->getRequestJSON(file_get_contents('php://input'));
$authentication->authenticateRequest($jsonBody);

$directory = new DirectoryIterator(dirname(__FILE__).'/control-files');
foreach ($directory as $fileinfo) {
    if (!$fileinfo->isDot()) {
        $file = $fileinfo->getBasename('.json');
        $people[$file] = json_decode(file_get_contents("control-files/$file.json"), true);
    }
}

echo json_encode($people);
?>