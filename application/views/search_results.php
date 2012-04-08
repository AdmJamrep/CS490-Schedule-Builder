<?PHP if(count($result->classes) > 0):?>
<table cellspacing = "1">

<tr>
	<th>NAME</th><th>DAYS/TIMES</th><th>STATUS</th><th>INSTRUCTOR</th><th>CREDITS</th><th>PREREQUISITES</th><th>ACTION</th>
</tr>

<?PHP $rowclass = 0; ?>
<?PHP foreach($result->classes as $call_number=>$class):?>


<tr class = "row<?PHP echo $rowclass ?>">
	<td>
		<?PHP echo $class->abbreviation?>-<?PHP echo $class->course_number?><br />
		<?PHP echo $class->section_number?><br />
		<?PHP echo $class->name?>
	</td>
	<td>
		<?PHP $class_list = $result->time_assoc[$call_number];?>
		<?PHP if($result->times[$class_list[0]]->start_datetime !== FALSE):?>
		<?PHP foreach($class_list as $t):?>
			<?PHP $time = $result->times[$t]?>
			<?PHP echo $time->start_datetime->format('D g:i')?>-<?PHP echo $time->end_datetime->format('g:i')?>
			<br />
		<?PHP endforeach;?>
		<?PHP else:?>
		Not Specified
		<?PHP endif;?>
	</td>
	<td><?PHP echo $class->status?> (<?PHP echo $class->current_size?>/<?PHP echo $class->max_size?>)</td>
	<td><?PHP echo $class->instructor?></td>
	<td><?PHP echo $class->credits?></td>
	<td><div><?PHP echo $class->{'pre-requisites'}?></div><div><?PHP echo $class->comments?></div></td>
	<td>
		<form method="post" id="add_form_<?PHP echo $call_number ?>" 
				action="javascript:add_course($('add_form_<?PHP echo $call_number ?>'))">
		<input type="hidden" name="call_number" value="<?PHP echo $call_number ?>"/>
		<?PHP if(empty($class->conflicts)):?>
			<input type = "submit" value = "Add" />
		<?PHP else:?>
			<?PHP foreach($class->conflicts as $conflicting_call=>$c):?>
				<?PHP if($conflicting_call != $call_number):?>
					<input type="hidden" name="conflicting_call[]" value="<?PHP echo $conflicting_call?>" />
				<?PHP endif;?>
			<?PHP endforeach;?>
			<input type = "submit" value = "Add Anyway" />
		<?PHP endif;?>
		</form>
	</td>
<?PHP $rowclass = 1 - $rowclass; ?>
<?PHP if(!empty($class->conflicts)):?>

</tr>

<tr>
<tr style="background-color:#fcc">
	<td colspan="7">
		
			Conflicts With:<br />
			<?PHP foreach($class->conflicts as $conflicting_call=>$c):?>
				<?PHP echo $c[0]->abbreviation?>-<?PHP echo $c[0]->course_number?> <?PHP echo $c[0]->section_number?> <?PHP echo $c[0]->name ?> (
				<?PHP foreach($c as $key=>$conflict):?>
					<?PHP if($key>0):?>,<?PHP endif;?>
					<?PHP echo $conflict->start_datetime->format('D g:i').'-'.
							$conflict->end_datetime->format('g:i')?>
				<?PHP endforeach;?>) 
			<?PHP endforeach;?>

	</td>
</tr>
<?PHP endif;?>
</tr>
<?PHP endforeach;?>
</table>
<?PHP else:?>
<h4>No Results Found</h4>
<?PHP endif;?>