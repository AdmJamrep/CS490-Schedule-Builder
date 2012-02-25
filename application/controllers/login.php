<?PHP
/**
 * DEPRECATED
**/
class Login extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	public function login_form()
	{
		$this->load->view('login_form');
	}
	public function do_login()
	{
		$data['username'] = $this->input->post('username');
		$this->db->insert('Alpha',$data);
		
		$this->load->view('output',$data);
	}
	public function view_all_usernames()
	{
		$data['data'] = $this->db->get('Alpha');
		$this->load->view('all_logins',$data);
	}
	public function newtest()
	{
		preg_match_all("|([a-z]+)>([a-z]+)>([a-z]+)>|U",
			"cs>nicholson>kupf> is>jones>gitc>",
			$out, PREG_PATTERN_ORDER);
		echo var_dump($out);
	}
}