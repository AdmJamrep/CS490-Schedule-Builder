<?PHP
class Schedule extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('schedule_model');
	}
	public function view($schedule_id)
	{
		$data = null;
		$data->result->schedule = $this->schedule_model->get_schedule($schedule_id);
		$data->result->grid = $this->course_list_model->make_grid($data->result->schedule);
		$data->result->editable = $this->schedule_model->get_schedule_id() == $schedule_id;
		$data->result->action = 'show_schedule';
		$data->load_schedule = TRUE;
		
		$this->load->view('main',$data);
	}
	public function new_schedule()
	{
		$this->schedule_model->create_schedule();
	}
	public function add_course()
	{
		$remove = $this->input->post('conflicting_call');
		if(!empty($remove))
		{
			$this->schedule_model->remove_classes($remove);
		}
		$call_number = $this->input->post('call_number');
		
		$result = $this->schedule_model->add_class($call_number);
		
		if($result !== FALSE)
			$this->load->view('ajax_success');
		else
		{
			$data = null;
			$data->message = 'Error: Unable to add course';
			$this->load->view('ajax_error',$data);
		}
	}
	public function unadd_course()
	{
		$call_number = $this->input->post('call_number');
		
		$result = $this->schedule_model->remove_classes($call_number);
		
		$remove = $this->input->post('conflicting_call');
		if(!empty($remove))
		{
			foreach($remove as $r)
			{
				$this->schedule_model->add_class($r);
			}
		}
		
		if($result !== FALSE)
			$this->load->view('ajax_success');
		else
		{
			$data = null;
			$data->message = 'Error: Unable to undo add course';
			$this->load->view('ajax_error',$data);
		}
	}
	public function remove_course()
	{
		$remove = $this->input->post('call_number');
		$this->schedule_model->remove_classes($remove);
	}
	public function show_schedule_grid()
	{
		$data = null;
		$data->schedule = $this->schedule_model->get_current_schedule();
		$data->grid = $this->course_list_model->make_grid($data->schedule);
		
		$this->load->view('schedule_grid',$data);
	}
	public function show_grid_as_image()
	{
		$data = null;
		$data->schedule = $this->schedule_model->get_current_schedule();
		$data->grid = $this->course_list_model->make_grid($data->schedule);
		
		$this->load->view('schedule_grid_image',$data);
	}
	public function channel()
	{
		$cache_expire = 60*60*24*365;
		header("Pragma: public");
		header("Cache-Control: max-age=".$cache_expire);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
		echo '<script src="//connect.facebook.net/en_US/all.js"></script>';
	}
	public function test_fb_js()
	{
		echo "
			<div id=\"fb-root\">
			  <!-- The JS SDK requires the fb-root element in order to load properly. -->
			</div>
			<script type='text/javascript' src='".str_replace('index.php/','',site_url('javascripts/prototype.js'))."'></script>
			<script>
			  window.fbAsyncInit = function() {
				FB.init({
				  appId      : '121479874650613', // App ID
				  channelUrl : '".site_url('schedule/channel')."', // Channel File
				  status     : true, // check login status
				  cookie     : true, // enable cookies to allow the server to access the session
				  xfbml      : true  // parse XFBML
				});

				// Additional initialization code here
			  };

			  // Load the SDK Asynchronously
			  (function(d){
				 var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
				 if (d.getElementById(id)) {return;}
				 js = d.createElement('script'); js.id = id; js.async = true;
				 js.src = \"//connect.facebook.net/en_US/all.js\";
				 ref.parentNode.insertBefore(js, ref);
			   }(document));
			   function do_login()
			   {
					 FB.login(function(response) {
						if (response.status === 'connected') {
							console.debug(response);
							//window.location = '".site_url('schedule/test_facebook')."';
						}
						else
						{
							alert('FAIL!');
						}
					 }, {scope: 'publish_stream'});
			   }
			</script>
			<a href='javascript:do_login()'>Test</a>
		";
	}
	public function test_facebook()
	{
		$this->load->library('facebook',array(
		  'appId'  => '	121479874650613',
		  'secret' => '	e78188cf514c774cf9d485b2fc97b18f',
		  'cookie' => TRUE,
		  'fileUpload' => TRUE
		));
		$this->facebook->setAccessToken('AAABufDzGofUBAFd13lvSxmmvQuaXTSuHxh7npIm2MLo1aqjDFvWXpV8ffacC5ZCfO4uYWvynKbVhNpBG0tRLRtzyqKBfF4NAgjZAUdIhVZBo3lup6mh');
		
		$user = $this->facebook->getUser();
		die(var_dump($user));
	}
	public function save_schedule()
	{
		$this->form_validation->set_rules('sched_name','Schedule Name','required|max_length[30]')->
			set_rules('email','Email', 'required|max_length[320]|valid_email')->
			set_rules('email_conf','Confirm Email','required|matches[email]')->
			set_error_delimiters('','');
		$has_schedule = $this->schedule_model->has_schedule();
		
		if($this->form_validation->run() && $has_schedule)
		{
			$name = $this->input->post('sched_name');
			$email = $this->input->post('email');
			$schedule_id = $this->schedule_model->save_schedule($name,$email);
			
			$data->schedule = $this->schedule_model->get_schedule();
			$data->schedule->is_saved = $this->schedule_model->is_saved();
			$data->schedule->schedule_id = $schedule_id;
			
			$this->load->view('schedule_list',$data);
		}
		else 
		{
			$data = null;
			if(!$has_schedule)
				$data->message = 'Error: You have not created a schedule or your session has expired.';
			else
				$data->message = validation_errors();
			
			$this->load->view('ajax_error',$data);
		}
	}
	public function request_access($schedule_id)
	{
		if(!empty($schedule_id))
		{
			$result = $this->schedule_model->request_access($schedule_id);
			if($result)
				$this->load->view('ajax_success');
			else
			{
				$data = null;
				$data->message = 'Error: Unable to request access';
				$this->load->view('ajax_error',$data);
			}
		}
		else
		{
			$data = null;
			$data->message = 'Error: No schedule identifier supplied in URL';
			$this->load->view('ajax_error',$data);
		}
	}
	public function confirm_access($code)
	{
		if(!empty($code))
		{
			$code = urldecode($code);
			$result = $this->schedule_model->confirm_access($code);
			if($result !== FALSE)
				redirect('schedule/view/'.$result);
			else
			{
				show_error('Error: Unable to request access');
			}
		}
		else
		{
			show_error('Error: No validation code supplied in URL');
		}
	}
}