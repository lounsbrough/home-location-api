<?PHP
class MyQ
{
    public function __construct()
    {
        require_once dirname(__FILE__).'/utilities.php';
        $this->utilities = new Utilities();
    }

    public function closeMainGarageDoor()
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/myq';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
            'door'=>'Main Door',
            'action'=>'close'
        );
        $postJSON = json_encode($postData);
        $this->utilities->postJSONRequest($url, $postJSON);
    }

    public function openMainGarageDoor()
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/myq';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
            'door'=>'Main Door',
            'action'=>'open'
        );
        $postJSON = json_encode($postData);
        $this->utilities->postJSONRequest($url, $postJSON);
    }
}
?>