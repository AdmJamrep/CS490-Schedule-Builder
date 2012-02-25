<?PHP
class Conflict_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
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
	 * Determines if two time-spans (represented by their respective
	 * start and end times) overlap
	 * @note this function requires PHP 5.2.2 or greater
	 * @param DateTime first_start start time of the first range
	 * @param DateTime first_end end time of the first range
	 * @param DateTime second_start start time of the second range
	 * @param DateTime second_end end time of the second range
	 * @return boolean TRUE if there is a conflict, FALSE otherwise
	**/
	public function has_conflict($first_start,$first_end,$second_start,$second_end)
	{
		if($first_start === FALSE || $second_start === FALSE)
			return FALSE;
		if($first_start == $second_start && $first_end == $second_end)
			return TRUE;
		else if($first_start > $second_start && $first_start < $second_end)
			return TRUE;
		else if ($second_start > $first_start && $second_start < $first_end)
			return TRUE;
		return FALSE;
	}
}