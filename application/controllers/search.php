<?PHP
class Search extends CI_Controller
{
	public function __construct()
	{
		$this->load->library('form_validation');
		parent::__construct();
	}
	public function do_search()
	{
		
		
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
				
				echo '<ui>';
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
				
				echo '<ui>';
				foreach($subjs->result() as $row)
				{
					echo '<li>'.$row->abbreviation.'</li>';
				}
				echo '</ul>';
			}
		}
	}
}