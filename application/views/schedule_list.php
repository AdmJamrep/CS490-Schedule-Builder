<!--Author: Brian Corzo-->
<?PHP if(!empty($schedule->classes)):?>
	Your Classes: <br />
	<?PHP foreach($schedule->classes as $s_class):?>
		<div>
		<?PHP echo $s_class->abbreviation?>-<?PHP echo $s_class->course_number?> <?PHP echo $s_class->name?>
		<a href="javascript:remove_course(<?PHP echo $s_class->call_number?>);">(Remove)</a>
		</div>
	<?PHP endforeach;?>
	<a href="javascript:show_schedule_grid()">Show Schedule Grid</a>
<?PHP else:?>
	Classes will appear here as you add them.
<?PHP endif;?>