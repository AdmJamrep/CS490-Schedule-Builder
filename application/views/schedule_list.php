<?PHP if(!empty($schedule->classes)):?>
	<?PHP foreach($schedule->classes as $s_class):?>
		<div>
		<?PHP echo $s_class->abbreviation?>-<?PHP echo $s_class->course_number?> <?PHP echo $s_class->name?>
		</div>
	<?PHP endforeach;?>
<?PHP else:?>
	Classes will appear here as you add them.
<?PHP endif;?>