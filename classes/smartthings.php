<?PHP
class SmartThings
{
    public function __construct()
    {
        require_once dirname(__FILE__).'/utilities.php';
        $this->utilities = new Utilities();
    }

    public function turnLightOn($deviceName)
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/smartthings';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
            'deviceName'=>$deviceName,
            'action'=>'turnLightOn'
        );
        $postJSON = json_encode($postData);
        $this->utilities->postJSONRequest($url, $postJSON);
    }

    public function turnLightOff($deviceName)
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/smartthings';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
            'deviceName'=>$deviceName,
            'action'=>'turnLightOff'
        );
        $postJSON = json_encode($postData);
        $this->utilities->postJSONRequest($url, $postJSON);
    }
}
?>