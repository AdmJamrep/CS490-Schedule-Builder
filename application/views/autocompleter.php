<ul>
<?PHP foreach($autocomplete as $row):?>
	<li <?PHP if(!empty($row->value)):?>title="<?PHP echo $row->value ?>"<?PHP endif;?>><?PHP echo $row->label ?><?PHP if(!empty($row->sec_label)):?> - <?PHP echo $row->sec_label ?><?PHP endif;?></li>
<?PHP endforeach;?>
</ul>