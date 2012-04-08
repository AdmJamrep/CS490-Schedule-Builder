<?PHP
class Autocompletion_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	public function get_professors($professor_name)
	{
		return $this->db->like('instructor',$professor_name)->
				group_by('instructor')->
				get('class_sections');
	}
	public function get_subjects($subject)
	{
		return $this->db->like('name',$subject)->
			or_like('abbreviation',$subject)->
			get('subjects');
	}
}