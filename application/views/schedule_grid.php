<!--Author: Brian Corzo-->
<<<<<<< HEAD
<!-- Schedule Grid -->
<link rel="stylesheet" type="text/css" href="<?PHP echo str_replace('index.php/', '', site_url('stylesheets/style.css'))?>" />
=======
<!-- The Schedule Table -->
<!-- Each cell should contain the class name and location -->
<link rel="stylesheet" type="text/css" href="<?PHP echo str_replace('index.php/', '', site_url('stylesheets/style.css'))?>" />



<div style= "width:100%; text-align:center">
<table id = "schedule_grid"><tr><td> 
>>>>>>> 46a11212a44cc724d5e7d0ab939eb3530ff1230e



<div style= "width:100%; text-align:center">
<table style = "text-align:center"><tr><td> 


<table class = "grid">
<tr>
<th>Day/Time</th>
<th>MON</th>
<th>TUE</th>
<th>WED</th>
<th>THU</th>
<th>FRI</th>
<th>SAT</th>
</tr>
<?PHP foreach($grid as $row):?>
	<?PHP if($row->row_occupied):?>
	<tr>
		<th>
			<?PHP echo $row->start_datetime->format('g:i') ?> <br>
			<?PHP echo $row->end_datetime->format('g:i') ?>
		</th>
		<?PHP foreach($row->blocks as $block):?>
			<?PHP if($block === NULL):?>
				<td>&nbsp;</td>
			<?PHP elseif($block !== FALSE):?>
				<td rowspan="<?PHP echo $block->rowspan ?>">
					<?PHP 
						$course = $schedule->classes[$block->call_number];
						$time = $schedule->times[$block->time_index];
					?>
					
					<?PHP echo $course->abbreviation.'-'.$course->course_number.' '.
							$course->section_number ?><br />
					<?PHP echo $time->room ?>
				</td>
			<?PHP endif;?>
		<?PHP endforeach;?>
	</tr>
	<?PHP endif;?>
<?PHP endforeach;?>


</table>
<!-- END Schedule Table -->
<br />
<table border="4">
<tr>
	<th>CALL NUMBER</th><th>CLASS</th><th>TITLE</th><th>DATE/TIME/LOCATION</th><th>INSTRUCTOR</th><th>CREDITS</th>
</tr>
<?PHP foreach($schedule->classes as $class):?>
	<tr>
		<td><?PHP echo $class->call_number?></td>
		<td>
			<?PHP echo $class->abbreviation?>-<?PHP echo $class->course_number?><br />
			#<?PHP echo $class->section_number?>
		</td>
		<td><?PHP echo $class->name ?></td>
		<td>
			<?PHP $class_list = $schedule->time_assoc[$class->call_number];?>
			<?PHP foreach($class_list as $t):?>
				<?PHP $time = $schedule->times[$t]?>
				<?PHP echo $time->start_datetime->format('D g:i')?>-<?PHP echo $time->end_datetime->format('g:i')?>
				<?PHP echo $time->room ?>
				<br />
			<?PHP endforeach;?>
		</td>
		<td><?PHP echo $class->instructor?></td>
		<td><?PHP echo $class->credits?></td>
	</tr>
<?PHP endforeach;?>
</table>

</td></tr></table>
</div>
