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
	public function make_grid($course_list)
	{
		$time_blocks = array(
			array('08:30:00','09:10:00'),
			array('09:15:00','09:55:00'),
			array('10:00:00','10:40:00'),
			array('10:45:00','11:25:00'),
			array('11:30:00','12:10:00'),
			array('12:15:00','12:55:00'),
			array('13:00:00','13:40:00'),
			array('13:45:00','14:25:00'),
			array('14:30:00','15:10:00'),
			array('15:15:00','15:55:00'),
			array('16:00:00','16:40:00'),
			array('16:45:00','17:25:00'),
			array('17:45:00','17:55:00'),
			array('18:00:00','18:40:00'),
			array('18:45:00','19:25:00'),
			array('19:30:00','20:10:00'),
			array('20:15:00','20:55:00'),
			array('21:05:00','21:35:00')
		);
		$grid = array();
		if(empty($course_list->times))
		{
			return $grid;
		}
		
		foreach($time_blocks as $times)
		{
			$row = null;
			$row->start_time = $times[0];
			$row->end_time = $times[1];
			$row->start_datetime = $this->make_time('M',$times[0]);
			$row->end_datetime = $this->make_time('M',$times[1]);
			$row->blocks = array();
			for($i = 0; $i < 6; $i++)
			{
				$row->blocks[] = NULL;
			}
			$grid[] = $row;
		}
		
		$days = 'MTWRFS';
		
		$min_time = $course_list->times[0]->start_time;
		$max_time = $course_list->times[count($course_list->times)-1]->end_time;
		
		$current_class = 0;
		foreach($grid as $rownum => $row)
		{
			$row->row_occupied = $this->in_block($row->start_time,$min_time,$max_time) ||
				$this->in_block($row->end_time,$min_time,$max_time);
			foreach($row->blocks as $day_offset => $block)
			{
				while(isset($course_list->times[$current_class]) &&
						$course_list->times[$current_class]->day === 'TBA')
				{
					$current_class++;
				}
				if($block === FALSE)
				{
					$row->row_occupied = TRUE;
					continue;
				}
				else if(isset($course_list->times[$current_class]) &&
						$this->in_block($course_list->times[$current_class]->start_time,
								$row->start_time,$row->end_time) && 
						strpos($days,$course_list->times[$current_class]->day) === $day_offset
				)
				{
					$i = $rownum + 1;
					while($i < count($grid) && 
							(
								$grid[$i]->end_time <= $course_list->times[$current_class]->end_time || 
								(
										$course_list->times[$current_class]->end_time > $grid[$i]->start_time && 
										$course_list->times[$current_class]->end_time <= $grid[$i]->end_time  
								)
							)
					)
					{
						$grid[$i]->blocks[$day_offset] = FALSE;
						$i++;
					}
					
					$block_object = null;
					$block_object->call_number = $course_list->times[$current_class]->call_number; 
					$block_object->rowspan = $i - $rownum;
					$block_object->time_index = $current_class;
					$row->blocks[$day_offset] = $block_object;
					
					$current_class++;
					$row->row_occupied = TRUE;
				}
			}
		}
		return $grid;
	}
	private function in_block($test_time,$start_time,$end_time)
	{
		return $test_time >= $start_time && $test_time <= $end_time;
	}
	/**
	 * comparison function for sorting dates and times by
	 * start_time, day ASC (to use SQL terms)
	 * @note this function requires PHP 5.2.2 or greater
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
		else if($time_and_location1->start_time <
			$time_and_location2->start_time)
		{
			return -1;
		}
		else if($time_and_location1->start_time > 
			$time_and_location2->start_time)
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