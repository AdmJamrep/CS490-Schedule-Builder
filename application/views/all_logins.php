<?PHP foreach($data->result() as $key=>$row):?>
	Username #<?PHP echo $key?> is <?PHP echo $row->username?><br />
<?PHP endforeach;?>