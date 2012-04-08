<?PHP
class Search extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		//$this->output->enable_profiler(TRUE);
		$this->load->model('schedule_model');
		$this->schedule_model->create_schedule_if_not_exists();
	}
	public function index()
	{
		$this->load->view('main');
	}
	public function basic_search()
	{
		$this->load->model('search_model');
		$this->load->model('conflict_model');
		
		$keyword = $this->input->post('keyword');
		if(!empty($keyword))
		{
			$keyword_match = array();
			if(preg_match('(((?:[a-zA-z]{2,4})|(?:[rR][0-9]{3}))[-|\s]?([0-9]{3})(?:[-|\s]([A-Z0-9]{3}))?)',
					$keyword,$keyword_match))
			{
				$subject = $keyword_match[1];
				$course_number = $keyword_match[2];
				$this->search_model->set_subjects($subject);
				$this->search_model->set_course_number($course_number);
				if(isset($keyword_match[3]))
				{
					$section_number = $keyword_match[3];
					$this->search_model->set_section_number($section_number);
				}
			}
			else
			{
				$this->search_model->set_keyword($keyword);
			}
		}
		$exclude = $this->input->post('exclude');
		if(!empty($exclude))
		{
			$this->search_model->set_exclude($exclude);
		}
		$time = $this->input->post('time');
		if(!empty($time) && $time != 'default')
		{
			if($time == 1)
			{
				$this->search_model->set_start_time('10:00');
			}
			if($time == 2)
			{
				$this->search_model->set_start_time('11:30');
			}
			if($time == 3)
			{
				$this->search_model->set_start_time('17:45');
			}
			$this->search_model->set_end_time('23:00');
		}
		
		$this->common_search_filters();
		
		$result = $this->search_model->set_semester('fall')->
			set_year('2012')->
			search();
		$schedule = $this->schedule_model->get_schedule();
		
		$this->conflict_model->compare_for_conflicts($result,$schedule);
		
		$data = null;
		$data->action = 'show_search_results';
		$data->result = $result;
		$data->schedule = $schedule;
		$data->schedule->is_saved = $this->schedule_model->is_saved();
		$data->schedule->schedule_id = $this->schedule_model->get_schedule_id();
		
		$this->load->view('panels',$data);
		
	}
	public function advanced_search()
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
		$subject = $this->input->post('subjects');
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
		$honors = $this->input->post('honors');
		if(!empty($honors) && $honors != 'default')
		{
			if($honors == 'show_honors')
			{
				$this->search_model->set_honors(TRUE);
			}
			if($honors == 'hide_honors')
			{
				$this->search_model->set_honors(FALSE);
			}
		}
		$online = $this->input->post('online');
		if(!empty($online) && $online != 'default')
		{
			if($online == 'show_online')
			{
				$this->search_model->set_online(TRUE);
			}
			if($online == 'hide_online')
			{
				$this->search_model->set_online(FALSE);
			}
		}
		
		$this->common_search_filters();
		
		$result = $this->search_model->set_semester('fall')->
			set_year('2012')->
			search();
		$schedule = $this->schedule_model->get_schedule();
		
		$this->conflict_model->compare_for_conflicts($result,$schedule);
		
		$data = null;
		$data->action = 'show_search_results';
		$data->result = $result;
		$data->schedule = $schedule;
		$this->load->view('panels',$data);
	
	}
	private function common_search_filters()
	{
		$date = $this->input->post('day');
		if(!empty($date) && $date != 'default')
		{
			$days = array();
			if($date == 'nMon')
			{
				$days = array('T','W','R','F');
			}
			else if($date == 'nFri')
			{
				$days = array('M','T','W','R');
			}
			$this->search_model->set_days($days);
		}
		$level = $this->input->post('course_level');
		if(!empty($level) && $level != 'default')
		{
			if($level == 1)
			{
				$this->search_model->set_level('lower');
			}
			if($level == 2)
			{
				$this->search_model->set_level('upper');
			}
			if($level == 3)
			{
				$this->search_model->set_level('graduate');
			}
		}
		$open = $this->input->post('show_open_sections');
		if(!empty($open))
		{
			$this->search_model->hide_closed();
		}
	}
	public function professor_autocomplete()
	{
		$this->load->model('autocompletion_model');
		$this->form_validation->set_rules('prof',
				'Professor','required|min_length[3]|max_length[100]');
		if($this->form_validation->run())
		{
			$profs = $this->autocompletion_model->get_professors(
					$this->input->post('prof'));
			if($profs->num_rows() > 0)
			{
				$data = null;
				$data->autocomplete = array();
				foreach($profs->result() as $row)
				{
					$auto_item = null;
					$auto_item->label = $row->instructor;
					$data->autocomplete[] = $auto_item;
				}
				$this->load->view('autocompleter',$data);
			}
		}
	}
	public function subject_autocomplete()
	{
		$this->load->model('autocompletion_model');
		$this->form_validation->set_rules('subj',
				'Subject','required|min_length[2]|max_length[100]');
		if($this->form_validation->run())
		{
			$subjs = $this->autocompletion_model->get_subjects(
					$this->input->post('subj'));
			if($subjs->num_rows() > 0)
			{
				$data = null;
				$data->autocomplete = array();
				foreach($subjs->result() as $row)
				{
					$auto_item = null;
					$auto_item->label = $row->abbreviation;
					$auto_item->sec_label = $row->name;
					$auto_item->value = $row->abbreviation;
					$data->autocomplete[] = $auto_item;
				}
				$this->load->view('autocompleter',$data);
			}
		}
	}
}