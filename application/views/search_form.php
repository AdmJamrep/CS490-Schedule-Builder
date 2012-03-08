<html>
<head>
<title>Search classes v1.0</title>
<script type="text/javascript" src="<?PHP echo str_replace('index.php/','',site_url('javascripts/prototype.js'))?>"></script>
<script type="text/javascript">
function submit_form()
{
	url = '<?PHP echo site_url('search/do_search')?>';
	new Ajax.Updater('full_search_results',url,{postBody:$('search_form').serialize()});
}
</script>
</head>

<body>
<form method="post" action="javascript:submit_form()" id="search_form">
<table>
<tr>
<td>
Specify criteria for classes
<br />
<br />

<!-- There should be an autocompleter for this textfield -->
Professor: <input type = "text" name = "prof" /><br />

<!-- Autocomplete for this too -->
Subject: <input type = "text" name = "subj" /><br />

<!-- If user selects an invalid time range (like 1pm to 10am), here's what happens:

	1. Both drop down lists will be assigned a value when a selection is made.
	2. If the first list's value is GREATER than the second list's value, then
	   it is an invalid selection and nothing will happen.
	3. If both values are equal, then it will just return all classes starting at
	   that time.
	4. Otherwise, it will return all classes STARTING from [first list's value] and [second list's value].
-->
Specify a time range:

Within: <select name = "start_time">
<option value = "08:30">8:30am</option>
<option value = "10:00">10:00am</option>
<option value = "11:30">11:30am</option>
<option value = "13:00">1:00pm</option>
<option value = "14:30">2:30pm</option>
<option value = "16:00">4:00pm</option>
<option value = "18:00">6:00pm</option>
</select>
to: <select name = "end_time">
<option value = "08:30">8:30am</option>
<option value = "10:00">10:00am</option>
<option value = "11:30">11:30am</option>
<option value = "13:00">1:00pm</option>
<option value = "14:30">2:30pm</option>
<option value = "16:00">4:00pm</option>
<option value = "18:00">6:00pm</option>
<option value = "21:00">9:00pm</option>
<option value = "21:45">9:45pm</option>
</select>
</td>

<td>
Filters:
<table>
<tr>
<td>
<input type = "checkbox" name = "date[]" value = "M" />Monday<br />
<input type = "checkbox" name = "date[]" value = "T" />Tuesday<br />
<input type = "checkbox" name = "date[]" value = "W" />Wednesday<br />
<input type = "checkbox" name = "date[]" value = "R" />Thursday<br />
<input type = "checkbox" name = "date[]" value = "F" />Friday<br />
<input type = "checkbox" name = "date[]" value = "S" />Saturday<br />
</td>
<td>
<input type = "checkbox" name = "undergrad" value = "undergrad" />Undergraduate class<br />
<input type = "checkbox" name = "grad" value = "grad" />Graduate class<br />
<input type = "checkbox" name = "rutgers" value = "rut" />Rutgers class<br />
<input type = "checkbox" name = "online" value = "online" />Distance Learning class<br />
<input type = "checkbox" name = "honors" value = "honors" />Honors class<br />

<input type = "submit" value = "Search" />
</td>
</tr>
</table>



</td>
</tr>
</table>
</form>


<div id="full_search_results"></div>

</body>


</html>
