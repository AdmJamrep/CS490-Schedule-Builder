<?PHP
class Search extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->output->enable_profiler(TRUE);
		$this->load->model('schedule_model');
		$this->schedule_model->create_schedule_if_not_exists();
	}
	public function index()
	{
		$this->load->view('search_form');
	}
	public function do_search()
	{
		
		$this->load->model('search_model');
		$this->load->model('conflict_model');
		
		$start_time = $this->input->post('start_time');
		$end_time = $this->input->post('end_time');
		if(!empty($start_time) && !empty($end_time))
		{
			$this->search_model->set_start_time($start_time)->
				set_end_time($end_time);
		}
		$subject = $this->input->post('subj');
		if(!empty($subject))
		{
			$this->search_model->set_subjects($subject);
		}
		$date_filters = $this->input->post('date');
		if(!empty($date_filters))
		{
			$this->search_model->set_days($date_filters);
		}
		$professor = $this->input->post('prof');
		if(!empty($professor))
		{
			$this->search_model->set_professor($professor);
		}
		$rutgers = $this->input->post('rutgers');
		if(empty($rutgers))
		{
			$this->search_model->set_rutgers(FALSE);
		}
		$honors = $this->input->post('honors');
		if(empty($honors))
		{
			$this->search_model->set_honors(FALSE);
		}
		$graduate = $this->input->post('grad');
		if(empty($graduate))
		{
			$this->search_model->set_graduate(FALSE);
		}
		$online = $this->input->post('online');
		if(empty($online))
		{
			$this->search_model->set_online(FALSE);
		}
		
		$result = $this->search_model->set_semester('fall')->
			set_year('2012')->
			search();
		
		$schedule = $this->schedule_model->get_schedule();
		
		$this->conflict_model->compare_for_conflicts($result,$schedule);
		
		$data = null;
		$data->result = $result;
		$data->schedule = $schedule;
		$this->load->view('search_results',$data);
		
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
			echo 'success';
		else
			show_error('Unable to add course');
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
	public function professor_autocomplete()
	{
		$this->form_validation->set_rules('prof',
				'Professor','required|min_length[3]|max_length[100]');
		if($this->form_validation->run())
		{
			$profs = $this->db->like('instructor',$this->
					input->post('prof'))->
					group_by('instructor')->
					get('class_sections');
			
			if($profs->num_rows() > 0)
			{
				
				echo '<ul>';
				foreach($profs->result() as $row)
				{
					echo '<li>'.$row->instructor.'</li>';
				}
				echo '</ul>';
			}
		}
	}
	public function subject_autocomplete()
	{
		$this->form_validation->set_rules('subj',
				'Subject','required|max_length[4]');
		if($this->form_validation->run())
		{
			$subjs = $this->db->like('abbreviation',$this->
					input->post('subj'))->
					get('subjects');
			
			if($subjs->num_rows() > 0)
			{
				
				echo '<ul>';
				foreach($subjs->result() as $row)
				{
					echo '<li>'.$row->abbreviation.'</li>';
				}
				echo '</ul>';
			}
		}
	}
}