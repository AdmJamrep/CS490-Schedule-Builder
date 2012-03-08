<?PHP
class Search extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
	}
	public function index()
	{
		$this->load->view('search_form');
	}
	public function do_search()
	{
		//$this->form_validation->set_rules('');
		
		$this->load->model('search_model');
		
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
			
		$this->load->view('search_results',$result);
		
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
					get('course_sections');
			
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