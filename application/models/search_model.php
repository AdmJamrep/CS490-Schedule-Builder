<?PHP
/**
 * TODO: remove debugging code (convert "assert" statements to
 * a more graceful error handler)
**/
class Search_model extends CI_Model
{
	private $keyword = '';
	private $start_time = '';
	private $end_time = '';
	private $subjects = array();
	private $days = array();
	private $rutgers = '';
	private $honors = '';
	private $graduate = '';
	private $tba = '';
	private $professor = '';
	private $semester = '';
	private $year = '';
	
	private $valid_days = array('M','T','W','R','F','S');
	private $valid_semesters = array('winter','spring','summer','fall');
	
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * sets the search keyword
	 * @param string keyword
	 * @return Search_model
	**/
	public function set_keyword($keyword)
	{
		assert(is_string($keyword) && !empty($keyword));
		
		$this->keyword = $keyword;
		return $this;
	}
	/**
	 * sets the start time
	 * @param string start_time
	 * @return Search_model
	**/
	public function set_start_time($start_time)
	{
		assert($this->is_valid_time($start_time));
		
		$this->start_time = $start_time.':00';
		return $this;
	}
	/**
	 * sets the end time
	 * @param string start_time
	 * @return Search_model
	**/
	public function set_end_time($end_time)
	{
		assert($this->is_valid_time($end_time));
		
		$this->end_time = $end_time.':00';
		return $this;
	}
	/**
	 * set the subject list
	 * @param mixed subjects a single subject or list of subjects
	 * @return Search_model
	**/
	public function set_subjects($subjects)
	{
		if(!is_array($subjects))
			$subjects = array($subjects);
		foreach($subjects as $s)
			assert(is_string($s));
		
		$this->subjects = array_merge($this->subjects,$subjects);
		return $this;
	}
	/**
	 * set the day list
	 * @param mixed days a single day or list of days
	 * @return Search_model
	**/
	public function set_days($days)
	{
		if(!is_array($days))
			$days = array($days);
		foreach($days as $d)
			assert(is_string($d) && in_array($d,$this->valid_days));
		
		$this->days = array_merge($this->days,$days);
		return $this;
	}
	/**
	 * sets the Rutgers course filtering value (i.e.
	 * whether to exclude all Rutgers courses [FALSE]
	 * or show *only* Rutgers courses [TRUE]). NOTE:
	 * to allow Rutgers courses without limiting
	 * the result to ONLY Rutgers, don't call this function
	 * at all.
	 * @param boolean rutgers
	 * @return Search_model
	**/
	public function set_rutgers($rutgers)
	{
		assert(is_bool($rutgers));
		
		$this->rutgers = $rutgers;
		return $this;
	}
	/**
	 * sets the honors course filtering value (see documentation
	 * of search_model->set_rutgers for more information on
	 * filtering values)
	 * @param boolean honors
	 * @return Search_model
	**/
	public function set_honors($honors)
	{
		assert(is_bool($honors));
		
		$this->honors = $honors;
		return $this;
	}
	/**
	 * sets the graduate course filtering value (see documentation
	 * of search_model->set_rutgers for more information on
	 * filtering values)
	 * @param boolean graduate
	 * @return Search_model
	**/
	public function set_graduate($graduate)
	{
		assert(is_bool($graduate));
		
		$this->graduate = $graduate;
		return $this;
	}
	/**
	 * sets the TBA course filtering value (see documentation
	 * of search_model->set_rutgers for more information on
	 * filtering values)
	 * @param boolean tba
	 * @return Search_model
	**/
	public function set_tba($tba)
	{
		assert(is_bool($tba));
		
		$this->tba = $tba;
		return $this;
	}
	/**
	 * sets the professor name filter
	 * @param string professor
	 * @return Search_model
	**/
	public function set_professor($professor)
	{
		assert(is_string($professor));
		
		$this->professor = $professor;
		return $this;
	}
	/**
	 * sets the semester (i.e. spring, summer, fall, or winter)
	 * @param string semester
	 * @return Search_model
	**/
	public function set_semester($semester)
	{
		assert(is_string($semester) && 
				in_array($semester,$this->valid_semesters));
		
		$this->semester = $semester;
		return $this;
	}
	/**
	 * Sets the year to search for. Will accept the current year
	 * or next year only
	 * @param string year
	 * @return Search_model
	**/
	public function set_year($year)
	{
		assert(is_numeric($year) && $this->is_valid_year($year));
		
		$this->year = $year;
		return $this;
	}
	/**
	 * Performs the search
	 * @return array of row objects from the course_sections 
	 * and courses tables, with an added field (called 
	 * time_and_location) containing an array corresponding 
	 * row objects from the time_and_location table
	**/
	public function search()
	{
		assert(!empty($this->semester) && !empty($this->year));
		assert(empty($this->start_time) || !empty($this->end_time));
		
		$this->load->model('course_list_model');
		
		$query_params = array();
		
		$query_string = '
				SELECT t1.*,c.*,co.*
				FROM time_and_location t1
				JOIN class_sections c
					ON c.call_number = t1.call_number
				JOIN courses co
					ON co.abbreviation = c.abbreviation
					AND co.course_number = c.course_number ';
		
		$day_questions = $this->course_list_model->print_question_marks(
				count($this->days));
		
		if(!empty($this->days) || !empty($this->start_time))
		{
			/**
			 * makes sure there aren't *other* times for the course
			 * outside the range the user is searching for
			**/
			$query_string .= '
					LEFT JOIN time_and_location t2
						ON t2.call_number = t1.call_number
						AND (';
			
			if(!empty($this->start_time))
			{
				if(!empty($this->days))
				{
					$query_string .= '
							(t2.day IN('.$day_questions.') AND ';
					$query_params = array_merge($query_params,$this->days);
				}
				$query_string .= '
						(t2.start_time NOT BETWEEN ? AND ? OR t2.end_time > ?)';
				$query_params[] = $this->start_time;
				$query_params[] = $this->end_time;
				$query_params[] = $this->end_time;
				if(!empty($this->days))
				{
					$query_string .= ')';
				}
			}
			if(!empty($this->days))
			{
				if(!empty($this->start_time))
				{
					$query_string .= ' OR';
				}
				$query_string .= ' 
						t2.day NOT IN('.$day_questions.')';
				$query_params = array_merge($query_params,$this->days);
			}
			$query_string .= ')';
			
		}
		$query_string .= '
		WHERE semester = ?
			AND year_offered = ? ';
		$query_params[] = $this->semester;
		$query_params[] = $this->year;
		if(!empty($this->days) || !empty($this->start_time))
		{
			if(!empty($this->start_time))
				$this->tba = FALSE;
			$query_string .= '
					AND t2.call_number IS NULL ';
		}
		if(!empty($this->days))
		{
			$query_string .= '
					AND t1.day IN ('.$day_questions.') ';
			$query_params = array_merge($query_params, $this->days);
		}
		if(!empty($this->start_time))
		{
			$query_string .= '
					AND t1.start_time BETWEEN ? AND ?
					AND t1.end_time <= ?';
			$query_params[] = $this->start_time;
			$query_params[] = $this->end_time;
			$query_params[] = $this->end_time;
		}
		if(!empty($this->subjects))
		{
			$query_string .= '
					AND abbreviation IN ('.$this->course_list_model->
							print_question_marks(count($this->subjects)).') ';
			$query_params = array_merge($query_params, $this->subjects);
		}
		if($this->rutgers === TRUE)
		{
			$query_string .= '
					AND abbreviation LIKE "R%" ';
		}
		else if($this->rutgers === FALSE)
		{
			$query_string .= '
					AND abbreviation NOT LIKE "R%" ';
		}
		if($this->honors === TRUE)
		{
			$query_string .= '
					AND section_number LIKE "H%" ';
		}
		else if($this->honors === FALSE)
		{
			$query_string .= '
					AND section_number NOT LIKE "H%" ';
		}
		if($this->graduate === TRUE)
		{
			$query_string .= '
					AND SUBSTR(course_number,1,1) IN ("5","6","7") ';
		}
		else if($this->graduate === FALSE)
		{
			$query_string .= '
					AND SUBSTR(course_number,1,1) NOT IN ("5","6","7") ';
		}
		if($this->tba === TRUE)
		{
			$query_string .= '
					AND t1.day = "TBA" ';
		}
		else if($this->tba === FALSE)
		{
			$query_string .= '
					AND t1.day <> "TBA" ';
		}
		if(!empty($this->professor))
		{
			$query_string .= '
					AND instructor = ? ';
			$query_params[] = $this->professor;
		}
		$result = $this->db->query($query_string,$query_params);
		$return = $this->course_list_model->format_course_list($result);
		
		return $return;
	}

	/**
	 * Off the shelf function to validate a time string
	 * (from http://snipplr.com/view/23007/validate-time/)
	 * @param string time
	 * @return boolean
	**/
	private function is_valid_time($time)
	{
		return preg_match("#([0-1]{1}[0-9]{1}|[2]{1}[0-3]{1}):[0-5]{1}[0-9]{1}#", $time)
			!= FALSE;
	}
	/**
	 * validates a year, making sure it's between the
	 * current year and next year
	 * @param string year
	 * @return boolean
	**/
	private function is_valid_year($year)
	{
		$year = (int)$year;
		$current_year = (int)date('Y');
		return $year == $current_year || 
				$year == $current_year + 1;
	}
}