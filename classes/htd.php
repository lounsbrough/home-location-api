<?PHP
class HTD
{
    public function __construct()
    {
        require_once dirname(__FILE__).'/utilities.php';
        $this->utilities = new Utilities();
    }

    public function turnOffAllSpeakers() 
    {
        $url = 'https://lounsbrough.ddns.net/htd/api/control.php';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
            'command'=>'powerOff'
        );
        $postJSON = json_encode($postData);
        $this->utilities->postJSONRequest($url, $postJSON);
    }
    
    public function turnOnUpstairsSpeakers()
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
        $this->utilities->postJSONRequest($url, $postJSON);
    }
}
?>