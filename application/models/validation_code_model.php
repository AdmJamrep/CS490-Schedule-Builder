<?PHP
class Validation_code_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * generates a cryptographically-strong random string
	 * @note requires OpenSSL, PHP 5.3.0+. Known to perform
	 * poorly on Windows servers, due to OpenSSL's implementation
	 * @return string
	**/
	private function generate_code()
	{
		$bytes = openssl_random_pseudo_bytes (32);
		return base64_encode($bytes);
	}
	/**
	 * Emails the user a randomly-generated
	 * validation code
	 * @param string email
	 * @param string view which view contains the email body
	 * @return mixed string with the validation code on success,
	 * boolean FALSE on failure
	**/
	public function email_code($email,$view,$data=null)
	{
		$this->load->library('email');
		$data->code = $this->generate_code();
		$body = $this->load->view($view,$data,TRUE);
		
		$result = $this->email->initialize(array('mailtype'=>'html'))->
			from('schedule_builder@njit.edu')->
			to($email)->
			subject('Access your saved schedule')->
			message($body)->
			send();
		if($result)
		{
			return $data->code;
		}
		return FALSE;
	}

}