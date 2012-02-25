<?PHP
class Unit_Testing extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('unit_test');
	}
	
	public function test()
	{
		echo 'good';
	}
	public function test_search_model()
	{
		$this->load->model('search_model');
		
		$this->search_model->set_semester('fall')->
			set_year('2012')->
			//set_subjects('TEST')->
			//set_days(array('M','T','W'))->
			//set_start_time('10:00')->
			//set_end_time('22:00')->
			set_honors(TRUE)->
			//set_tba(FALSE)->
			search();
	}
	public function test_conflict_model()
	{
		$this->load->model('conflict_model');
		
		$monday = new DateTime('2012-02-20 14:00:00');
		$tuesday = new DateTime('2012-02-21 14:00:00');
		$wednesday = new DateTime('2012-02-22 14:00:00');
		$thursday = new DateTime('2012-02-23 14:00:00');
		$friday = new DateTime('2012-02-24 14:00:00');
		$saturday = new DateTime('2012-02-25 14:00:00');
		$tba = FALSE;
		
		$this->unit->run($this->conflict_model->make_time('M','14:00:00'),
			$monday,'Monday');
		$this->unit->run($this->conflict_model->make_time('T','14:00:00'),
			$tuesday,'Tuesday');
		$this->unit->run($this->conflict_model->make_time('W','14:00:00'),
			$wednesday,'Wednesday');
		$this->unit->run($this->conflict_model->make_time('R','14:00:00'),
			$thursday,'Thursday');
		$this->unit->run($this->conflict_model->make_time('F','14:00:00'),
			$friday,'Friday');
		$this->unit->run($this->conflict_model->make_time('S','14:00:00'),
			$saturday,'Saturday');
		$this->unit->run($this->conflict_model->make_time('TBA',''),
			$tba,'TBA');
		
		$date_1 = new DateTime('2012-01-01 1:00:00');
		$date_2 = new DateTime('2012-01-01 2:00:00');
		$date_3 = new DateTime('2012-01-01 3:00:00');
		$date_4 = new DateTime('2012-01-01 4:00:00');
		$date_5 = new DateTime('2012-01-01 5:00:00');

		$this->unit->run($this->conflict_model->has_conflict($date_1,$date_3,$date_2,$date_4), 
			TRUE, '1 Overlaps 2');
		$this->unit->run($this->conflict_model->has_conflict($date_2,$date_4,$date_1,$date_3), 
			TRUE, '2 Overlaps 1');
		$this->unit->run($this->conflict_model->has_conflict($date_1,$date_4,$date_2,$date_3), 
			TRUE, '1 surrounds 2');
		$this->unit->run($this->conflict_model->has_conflict($date_1,$date_2,$date_1,$date_2), 
			TRUE, '1 Matches 2');
		$this->unit->run($this->conflict_model->has_conflict($date_1,$date_2,$date_4,$date_5), 
			FALSE, 'disjoint');
		$this->unit->run($this->conflict_model->has_conflict($date_1,$date_3,$date_3,$date_4), 
			FALSE, 'shared point');
		
		echo $this->unit->report();
	}
}