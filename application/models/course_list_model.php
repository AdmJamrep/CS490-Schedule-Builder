<?PHP
class Course_list_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	public function format_course_list($result)
	{
		$return = null;
		$return->classes = array();
		$return->times = array();
		$return->time_assoc = array();
		
		if($result->num_rows() > 0)
		{
			$current_call = 0;
			foreach($result->result() as $row)
			{
				$time_and_location = null;
				$time_and_location->day = $row->day;
				$time_and_location->start_time = $row->start_time;
				$time_and_location->end_time = $row->end_time;
				$time_and_location->room = $row->room;
				$time_and_location->call_number = $row->call_number;
				
				
				$time_and_location->start_datetime = $this->make_time(
						$row->day,$row->start_time);
				$time_and_location->end_datetime = $this->make_time(
						$row->day,$row->end_time);
				
				if($current_call != $row->call_number)
				{
					$current_call = $row->call_number;
					unset($row->day,$row->start_time,$row->end_time,
							$row->room);
					$return->classes[$row->call_number] = $row;
				}
				$return->times[] = $time_and_location;
			}
			
			//sort the session list by date and time
			usort($return->times,array($this,'date_and_time_compare'));
			
			foreach($return->times as $key=>$session)
			{
				if(!isset($return->time_assoc[$session->call_number]))
				{
					$return->time_assoc[$session->call_number] = array();
				}
				$return->time_assoc[$session->call_number][] = $key;
			}
		}
		return $return;
	}
	/**
	 * comparison function for sorting dates and times by
	 * start_time, day ASC (to use SQL terms)
	**/
	public function date_and_time_compare($time_and_location1,
			$time_and_location2)
	{
		//identical times on identical days tie
		if($time_and_location1->start_datetime == 
			$time_and_location2->start_datetime)
		{
			return 0;
		}
		//if 1 is tba, it's later than 2
		else if($time_and_location1->start_datetime === FALSE)
		{
			return -1;
		}
		//if 2 is tba, it's later than 1
		else if($time_and_location2->start_datetime === FALSE)
		{
			return 1;
		}
		//next we compare based on time of day, deliberately 
		//ignoring which day it's on
		else if($time_and_location1->start_date <
			$time_and_location2->start_date)
		{
			return -1;
		}
		else if($time_and_location1->start_date > 
			$time_and_location2->start_date)
		{
			return 1;
		}
		//if we got this far, they must be the same time on 
		//different days, so compare based off day
		else if($time_and_location1->start_datetime <
			$time_and_location2->start_datetime)
		{
			return -1;
		}
		else if($time_and_location1->start_datetime > 
			$time_and_location2->start_datetime)
		{
			return 1;
		}
	}
	/**
	 * Makes a DateTime object from the supplied
	 * date and time
	 * @param string date
	 * @param string time
	 * @return mixed (FALSE if the time is invalid
	 * [such as a TBA class], a DateTime object otherwise)
	**/
	public function make_time($date,$time)
	{
		$time_string = '';
		switch ($date)
		{
			/**
			 * NOTE: the only significance of these
			 * dates is that they are valid and have
			 * the correct day-of-week
			**/
			case 'M':
				$time_string = '2012-02-20';
				break;
			case 'T':
				$time_string = '2012-02-21';
				break;
			case 'W':
				$time_string = '2012-02-22';
				break;
			case 'R':
				$time_string = '2012-02-23';
				break;
			case 'F':
				$time_string = '2012-02-24';
				break;
			case 'S':
				$time_string = '2012-02-25';
				break;
			default:
				return FALSE;
				break;
		}
		$time_string .= ' '.$time;
		return new DateTime($time_string);
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