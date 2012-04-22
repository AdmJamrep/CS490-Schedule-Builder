<?PHP
/**
 * @author Donald Winship
**/
class Schedule_model extends CI_Model
{
	private $courses = array();
	private $schedule_id;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('course_list_model');
		
		$this->schedule_id = (int)$this->session->userdata('schedule_id');
		if($this->schedule_id > 0)
			$this->get_schedule($this->schedule_id);
	}
	/**
	 * Retrieves a user's schedule from the database
	 * @param int schedule_id unique numeric identifier 
	 * of the schedule
	 * @return array of row objects from the course_sections, 
	 * saved_schedules, and courses tables, with an added field (called 
	 * time_and_location) containing an array corresponding 
	 * row objects from the time_and_location table
	**/
	public function get_schedule($schedule_id='')
	{
		if($schedule_id == '')
			$schedule_id = $this->schedule_id;
		$result = $this->db->from('saved_schedules ss')->
			join('saved_schedule_classes ssc',
					'ssc.num_identifier = ss.num_identifier')->
			join('class_sections c',
					'c.call_number = ssc.call_number')->
			join('time_and_location t1',
					't1.call_number = c.call_number')->
			join('courses co','co.abbreviation = c.abbreviation '.
					'AND co.course_number = c.course_number '.
					'AND co.year_offered = c.year_offered '.
					'AND co.semester = c.semester ')->
			where('ss.num_identifier',$schedule_id)->
			select('*,ss.name schedule_name')->
			get();
		
		$this->courses = $this->course_list_model->format_course_list($result);
		$this->courses->is_saved = 0;
		$this->courses->can_edit = FALSE;
		$this->courses->name = '';
		$this->courses->schedule_id = $schedule_id;
		if($result->num_rows() > 0)
		{
			$this->courses->is_saved = $result->row()->saved_flag > 0;
			$this->courses->name = $result->row()->schedule_name;
			$this->courses->can_edit = $schedule_id == $this->schedule_id;
		}
		return $this->courses;
	}
	/**
	 * retrieve the contents of the schedule the user is currently
	 * working on
	 * @return see schedule_model->get_schedule
	**/
	public function get_current_schedule()
	{
		return $this->courses;
	}
	/**
	 * create a new schedule in the database *only*
	 * if the user doesn't currently have one
	**/
	public function create_schedule_if_not_exists()
	{
		if(empty($this->schedule_id))
		{
			$this->create_schedule();
		}
	}
	/**
	 * create a new schedule in the database and make that one the
	 * current schedule
	**/
	public function create_schedule()
	{
		$insert = null;
		$insert->name = '';
		$insert->saved_flag = 0;
		$this->db->insert('saved_schedules',$insert);
		$this->schedule_id = $this->db->insert_id();
		
		$this->session->set_userdata('schedule_id',$this->schedule_id);
		$this->session->set_userdata('is_saved',FALSE);
		return $this->schedule_id;
	}
	/**
	 * add a class to the current schedule
	 * @param int call_number
	 * @return mixed boolean FALSE if the course is invalid or the user
	 * has too many courses already, a CI_DB_Result object with conflict 
	 * information attached if there is a conflict found, and boolean TRUE
	 * on success
	**/
	public function add_class($call_number)
	{
		if(count($this->courses)>10)
			return FALSE;
		
		$this->load->model('conflict_model');
		$times = $this->db->where('call_number',$call_number)->
			get('time_and_location');
		if($times->num_rows() > 0)
		{
			$list = $this->course_list_model->format_course_list($times);
			
			$conflict_free = $this->conflict_model->compare_for_conflicts(
					$list,$this->courses);
			
			if($conflict_free === FALSE)
			{
				return $list;
			}
			else if($conflict_free === NULL)
			//the course is *already* in the user's
			//schedule
			{
				return TRUE;
			}
			else
			{
				$insert = null;
				$insert->num_identifier = $this->schedule_id;
				$insert->call_number = $call_number;
				$this->db->insert('saved_schedule_classes',$insert);
				
				$this->schedule_model->get_schedule();
				
				return TRUE;
			}
		}
		return FALSE;
	}
	/**
	 * remove one or more classes from the current
	 * schedule
	 * @param mixed call_numbers either an int or int[]
	 * of the call numbers to remove
	**/
	public function remove_classes($call_numbers)
	{
		if(!is_array($call_numbers))
		{
			$call_numbers = array($call_numbers);
		}
		$this->db->where('num_identifier',$this->schedule_id)->
			where_in('call_number',$call_numbers)->
			delete('saved_schedule_classes');
		
		$this->schedule_model->get_schedule();
	}
	public function has_schedule()
	{
		return $this->schedule_id > 0;
	}
	public function get_schedule_id()
	{
		return $this->schedule_id;
	}
	public function is_saved()
	{
		return  $this->session->userdata('is_saved');
	}
	/**
	 * mark a schedule to be permanently saved
	 * @param string name
	**/
	public function save_schedule($name,$email)
	{
		$this->db->set('name',$name)->
			set('saved_flag',1)->
			set('email',$email)->
			where('num_identifier',$this->schedule_id)->
			update('saved_schedules');
		$this->session->set_userdata('is_saved',TRUE);
		return $this->schedule_id;
	}
	public function request_access($schedule_id)
	{
		$schedule = $this->db->where('num_identifier',$schedule_id)->
			get('saved_schedules');
		
		if($schedule->num_rows() > 0)
		{
			$data = $schedule->row();
			$this->load->model('validation_code_model');
			
			$code = $this->validation_code_model->email_code($data->email,
					'confirmation_email',$data);
			
			if($code !== FALSE)
			{
				$insert = null;
				$insert->code = $code;
				$insert->num_identifier = $schedule_id;
				$this->db->insert('saved_schedule_codes',$insert);
				return TRUE;
			}
		}
		return FALSE;
	}
	public function confirm_access($code)
	{
		$validate_code = $this->db->where('code',$code)->
			where('creation_date >','DATE_SUB( NOW( ) , INTERVAL 1 DAY)',FALSE)->
			get('saved_schedule_codes');
		if($validate_code->num_rows() > 0)
		{
			$schedule_id = $validate_code->row()->num_identifier;
			$this->session->set_userdata('schedule_id',$schedule_id);
			$this->session->set_userdata('is_saved',TRUE);
			return $schedule_id;
		}
		return FALSE;
	}
}