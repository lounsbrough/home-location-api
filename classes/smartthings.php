<?PHP
class SmartThings
{
    public function __construct()
    {
        require_once dirname(__FILE__).'/utilities.php';
        $this->utilities = new Utilities();
    }

    public function setSwitchPowerState($deviceName, $power)
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/smartthings';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
            'deviceName'=>$deviceName,
            'action'=>'turnSwitch' . ($power ? 'On' : 'Off')
        );
        $postJSON = json_encode($postData);
        $this->utilities->postJSONRequest($url, $postJSON);
    }

    public function setAllSwitchesPowerState($power, $exceptions = null)
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/smartthings';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
            'action'=>'turnAllSwitches' . ($power ? 'On' : 'Off')
        );

        if ($exceptions != null) {
            $postData['exceptions'] = $exceptions;
        }

        $postJSON = json_encode($postData);
        $this->utilities->postJSONRequest($url, $postJSON);
    }
}
?>