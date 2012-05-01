<!--Author: Brian Corzo-->
<!-- Schedule Grid -->
<link rel="stylesheet" type="text/css" href="<?PHP echo str_replace('index.php/', '', site_url('stylesheets/style.css'))?>" />

<div style= "width:100%; text-align:center">
<table style = "text-align:center"><tr><td> 

<?PHP
	$cnt = 0;
	foreach($schedule->classes as $call_number => $row)
	{
		$cnt++;
		if ($cnt > 6)
			$cnt = 1;
	
		$schedule->classes[$call_number]->color_code = 'color_code' . $cnt;
	}
?>

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
				<?PHP 
						$course = $schedule->classes[$block->call_number];
						$time = $schedule->times[$block->time_index];
				?>
				<td class = "<?PHP echo $course->color_code ?>" rowspan="<?PHP echo $block->rowspan ?>">
					<strong><?PHP echo $time->start_datetime->format('g:i')?>-<?PHP echo $time->end_datetime->format('g:i')?></strong>
					<br />
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
<table class = "list" cellpadding = "4" cellspacing = "1">
<tr>
	<th>CALL NUMBER</th><th>CLASS</th><th>TITLE</th><th>DATE/TIME/LOCATION</th><th>INSTRUCTOR</th><th>CREDITS</th>
</tr>
<?PHP $rowclass = 0; ?>
<?PHP foreach($schedule->classes as $class):?>
	<tr class = "row<?PHP echo $rowclass ?>">
		<td><?PHP echo $class->call_number?></td>
		<td>
			<?PHP echo $class->abbreviation?>-<?PHP echo $class->course_number?><br />
			#<?PHP echo $class->section_number?>
		</td>
		<td><?PHP echo $class->name ?></td>
		<td>
			
			<?PHP $class_list = $schedule->time_assoc[$class->call_number];?>
			<?PHP if($schedule->times[$class_list[0]]->start_datetime !== FALSE):?>
				<?PHP foreach($class_list as $t):?>
					<?PHP $time = $schedule->times[$t]?>
					<strong><?PHP echo $time->start_datetime->format('D g:i')?>-<?PHP echo $time->end_datetime->format('g:i')?></strong>
					<?PHP echo $time->room ?>
					<br />
				<?PHP endforeach;?>
			<?PHP else:?>
				Not Specified
			<?PHP endif;?>
		</td>
		<td><?PHP echo $class->instructor?></td>
		<td><?PHP echo $class->credits?></td>
	</tr>
<?PHP $rowclass = 1 - $rowclass; ?>
<?PHP endforeach;?>
</table>

</td></tr></table>
</div>
