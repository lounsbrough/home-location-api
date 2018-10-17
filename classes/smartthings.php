<?PHP
class SmartThings
{
    public function __construct()
    {
        require_once dirname(__FILE__).'/utilities.php';
        $this->utilities = new Utilities();
    }

    public function turnOnTrayLight()
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/smartthings';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
            'deviceName'=>'Tray Light',
            'action'=>'turnLightOn'
        );
        $postJSON = json_encode($postData);
        $this->utilities->postJSONRequest($url, $postJSON);
    }

    public function turnOffTrayLight()
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/smartthings';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
            'deviceName'=>'Tray Light',
            'action'=>'turnLightOff'
        );
        $postJSON = json_encode($postData);
        $this->utilities->postJSONRequest($url, $postJSON);
    }
}
?>