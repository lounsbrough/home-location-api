<?PHP
class Pushbullet
{
    public function __construct()
    {
        require_once dirname(__FILE__).'/utilities.php';
        $this->utilities = new Utilities();
    }

    public function pushNote($deviceName, $noteTitle, $noteBody)
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/pushbullet';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET'),
            'deviceName'=>$deviceName,
            'action'=>'pushNote',
            'noteTitle'=>$noteTitle,
            'noteBody'=>$noteBody
        );
        $postJSON = json_encode($postData);
        
        return json_decode($this->utilities->postJSONRequest($url, $postJSON), true);
    }
}
?>