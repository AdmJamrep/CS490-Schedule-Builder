<?PHP
class Conflict_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
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
		else if($first_start >= $second_start && $first_start <= $second_end)
			return TRUE;
		else if ($second_start >= $first_start && $second_start <= $first_end)
			return TRUE;
		return FALSE;
	}
	/**
	 * compares two lists of courses (generally, one
	 * or more classes the user is looking at to their current
	 * schedule) to determine if there are time conflicts. If a 
	 * conflict is found, information on it will be attached to 
	 * the rows in the first course list supplied.
	 * @param CI_DB_Result first_course_list
	 * @param CI_DB_Result second_course_list
	 * @param boolean break_on_conflict controls whether
	 * this process will stop on the first conflict found 
	 * (optional, default FALSE)
	 * @return boolean TRUE if no conflicts are found, NULL if 
	 * this course is a duplicate, boolean FALSE if another error
	 * occurs
	**/
	public function compare_for_conflicts($first_course_list, $second_course_list, 
			$break_on_conflict=FALSE)
	{
		$result = TRUE;
		if(empty($second_course_list))
			return $result;
		
		foreach($first_course_list->times as $session1)
		{
			foreach($second_course_list->times as $session2)
			{
				/**
				 * these conditions that indicate that no conflict can
				 * exist between two sessions of a given course section:
				 * the sessions are on different days
				 * the sessions are on the same day, but both TBA
				**/
				if($session1->day != $session2->day || 
						$session1->start_datetime === FALSE)
				{
					continue;
				}
				
				$in_conflict = $this->has_conflict($session1->start_datetime,
						$session1->end_datetime,$session2->start_datetime,
						$session2->end_datetime);
				
				if($in_conflict)
				{
					$conflicting = $second_course_list->
							classes[$session2->call_number];
					/**
					 * if the courses are in conflict, attach information
					 * about the conflicting course (both for display on
					 * the UI and one-click conflict resolution) to the 
					 * first course list
					**/
					$conflicting_class = $second_course_list->
							classes[$session2->call_number];
					$conflict = null;
					$conflict->name = $conflicting_class->name;
					$conflict->start_datetime = $session2->start_datetime;
					$conflict->end_datetime = $session2->end_datetime;
					
					$first_course_list->classes[$session1->call_number]->
							conflicts[$session2->call_number][] = $conflict;
					
					if($break_on_conflict !== FALSE)
					{
						return FALSE;
					}
					$result = FALSE;
				}
			}
		}
		return $result;
	}
	
}