<!--Author: Brian Corzo-->
<div style="float:left; width:15%; padding-right:10px;" id="side_panel">
<?PHP $this->load->view('schedule_list',$schedule);?>
</div>
<div style="width:100%;" id="main_panel">
<?PHP 
$data = NULL;
if($action == 'show_search_results'):
	$data->result = $result;
	$this->load->view('search_results',$data);
else:
	$data->schedule = $schedule;
	$data->grid = $grid;
	$this->load->view('schedule_grid',$data);
endif;
?>
</div>
