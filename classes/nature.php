<?PHP
class Nature
{
    public function __construct()
    {
        require_once dirname(__FILE__).'/utilities.php';
        $this->utilities = new Utilities();
    }

    public function dayOrNight()
    {
        $url = 'https://lounsbrough.ddns.net/nature/sunrise-sunset/api.php';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET')
        );
        $postJSON = json_encode($postData);
        $responseJSON = json_decode($this->utilities->postJSONRequest($url, $postJSON), true);

        if ($responseJSON != null)
        {
            return $responseJSON['dayOrNight'];
        }
    }
}
?>