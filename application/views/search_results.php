<div style="float:left; width: 200px; padding-right:10px;">
Course List goes here
</div>
<div style="float:left; width:800; ">
<table cellspacing = "10">

<tr>
	<th>NAME</th><th>DAYS/TIMES</th><th>STATUS</th><th>NOW</th><th>MAX</th><th>INSTRUCTOR</th><th>CREDITS</th><th>PREREQUISITES</th><th>ACTION</th>
</tr>
<?PHP foreach($classes as $call_number=>$class):?>
<tr>
	<td><?PHP echo $class->name?></td>
	<td>
		<?PHP $class_list = $time_assoc[$call_number];?>
		<?PHP foreach($class_list as $t):?>
			<?PHP $time = $times[$t]?>
			<?PHP echo $time->start_datetime->format('D g:i A')?> - 
			<?PHP echo $time->end_datetime->format('g:i A')?>
			<br />
		<?PHP endforeach;?>
	</td>
	<td><?PHP echo $class->status?></td>
	<td><?PHP echo $class->current_size?></td>
	<td><?PHP echo $class->max_size?></td>
	<td><?PHP echo $class->instructor?></td>
	<td><?PHP echo $class->credits?></td>
	<td><?PHP echo $class->{'pre-requisites'}?></td>
	<td><input type = "button" value = "Add" /></td>
</tr>
<?PHP endforeach;?>
</table>
</div>
