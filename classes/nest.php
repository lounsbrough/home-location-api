<?PHP
class Nest
{
    public function __construct()
    {
        require_once dirname(__FILE__).'/utilities.php';
        $this->utilities = new Utilities();
    }

    public function getTemperature() 
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/nest/api/get-temperature.php';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET')
        );
        $postJSON = json_encode($postData);
        
        return json_decode($this->utilities->postJSONRequest($url, $postJSON), true);
    }

    public function getHumidity() 
    {
        $url = 'https://'.getenv('PUBLIC_SERVER_DNS').'/nest/api/get-humidity.php';
        $postData = array(
            'authCode'=>getenv('HTTPS_AUTHENTICATION_SECRET')
        );
        $postJSON = json_encode($postData);

        return json_decode($this->utilities->postJSONRequest($url, $postJSON), true);
    }
}
?>