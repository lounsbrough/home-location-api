<?PHP
class MyQ
{
    public function __construct()
    {
        require_once dirname(__FILE__).'/utilities.php';
        $this->utilities = new Utilities();
    }

    public function closeGarageDoor($doorName)
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/myq';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
            'door'=>$doorName,
            'action'=>'close'
        );
        $postJSON = json_encode($postData);
        $this->utilities->postJSONRequest($url, $postJSON);
    }

    public function openGarageDoor($doorName)
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/myq';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
            'door'=>$doorName,
            'action'=>'open'
        );
        $postJSON = json_encode($postData);
        $this->utilities->postJSONRequest($url, $postJSON);
    }
}
?>