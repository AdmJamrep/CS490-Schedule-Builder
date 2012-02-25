<?PHP
class Course_list_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * Helper function for other models: takes a raw class list
	 * (formed by a JOIN between other tables and the 
	 * time_and_location table) and splits the time and location
	 * off, eliminating duplicate info from the other tables
	 * in the process.
	 * @param CI_DB_Result result
	 * @return array of row objects from other tables, 
	 * with an added field (called time_and_location)
	 * containing an array corresponding row objects from 
	 * the time_and_location table
	**/
	public function format_class_list($result)
	{
		$return = array();
		if($result->num_rows() > 0)
		{
			$current_call = 0;
			$i = -1;
			foreach($result->result() as $row)
			{
				$time_and_location = null;
				$time_and_location->day = $row->day;
				$time_and_location->start_time = $row->start_time;
				$time_and_location->end_time = $row->end_time;
				$time_and_location->room = $row->room;
				if($current_call != $row->call_number)
				{
					unset($row->day,$row->start_time,$row->end_time,
							$row->room);
					$i++;
					$return[$i] = $row;
				}
				$return[$i]->time_and_location[] = $time_and_location;
			}
		}
		return $return;
	}
	/**
	 * Helper function, prints out a certain number of comma-separated
	 * question marks -- used for writing a SQL IN() list using
	 * CodeIgniter's query bindings feature
	 * @param int count
	 * @return string
	**/
	public function print_question_marks($count)
	{
		if($count == 0)
			return '';
		$result = '?';
		for($i = 1; $i < $count; $i++)
			$result .= ',?';
		return $result;
	}
}