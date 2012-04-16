<!--Author: Brian Corzo-->
<?PHP if(!empty($schedule->classes)):?>
	<?PHP if($schedule->can_edit):?>Your<?PHP else:?><?PHP echo $schedule->name.'&#39;s'?><?PHP endif;?> Classes: <br />
	<div>
	<?PHP foreach($schedule->classes as $s_class):?>
		<div>
		<?PHP echo $s_class->abbreviation?>-<?PHP echo $s_class->course_number?> <?PHP echo $s_class->section_number?> <?PHP echo $s_class->name?>
		<?PHP if($schedule->can_edit):?>
		<a href="javascript:remove_course(<?PHP echo $s_class->call_number?>);">(Remove)</a>
		<?PHP endif;?>
		</div>
	<?PHP endforeach;?>
	</div><br />
	<?PHP if($schedule->can_edit):?>
	<a href="javascript:show_schedule_grid()">Show Schedule Grid</a><br />
	<?PHP endif;?>
	<?PHP if(!$schedule->is_saved):?>
		<a href="javascript:show_email()">Save this Schedule</a><br />
		<div id = "email" style="display:none">
			<form method = "post" action = "javascript:save_schedule()" id = "save_sched">
				Schedule Name: <input type = "text" id = "sched_name" name = "sched_name" size ="12" /><br />
				Email: <input type = "text" name = "email" size = "12" /><br />
				Confirm Email: <input type = "text" name = "email_conf" size = "12" /><br />
			<input type = "submit" value = "Submit" />
		</form>
		</div>
	<?PHP else:?>
		Permalink: <input type="text" size = "15" value="<?PHP echo site_url('schedule/view/'.$schedule->schedule_id)?>"/><br />
		<a href="javascript:share_on_facebook()">Share on Facebook</a><br />
	<?PHP endif;?>
	<a href="<?PHP if($schedule->can_edit):?>javascript:new_schedule()<?PHP else:?><?PHP echo site_url('search')?><?PHP endif;?>">Start New Schedule</a>
<?PHP else:?>
	Classes will appear here as you add them.
<?PHP endif;?>
