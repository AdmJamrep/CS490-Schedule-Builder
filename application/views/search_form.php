<html>
<head>
<title>Search classes v1.0</title>
<script type="text/javascript" src="<?PHP echo str_replace('index.php/','',site_url('javascripts/prototype.js'))?>"></script>
<script type="text/javascript" src="<?PHP echo str_replace('index.php/','',site_url('javascripts/scriptaculous.js'))?>"></script>
<script type="text/javascript">
function submit_form()
{
	url = '<?PHP echo site_url('search/do_search')?>';
	new Ajax.Updater('full_search_results',url,{postBody:$('search_form').serialize()});
}
function add_course(myform)
{
	url = '<?PHP echo site_url('search/add_course')?>';
	new Ajax.Request(url,{postBody:myform.serialize(),onSuccess:submit_form});
}
function remove_course(course_id)
{
	url = '<?PHP echo site_url('search/remove_course')?>';
	new Ajax.Request(url,{postBody:'call_number='+course_id,onSuccess:submit_form});
}
function show_schedule_grid()
{
	url = '<?PHP echo site_url('search/show_schedule_grid')?>';
	new Ajax.Updater('full_search_results',url);
}
</script>
<style>
body, td
{
	font-family: Arial,Halvetica,Sans-Serif;
	font-size:12px;
}
/*based off http://madrobby.github.com/scriptaculous/ajax-autocompleter/ */
div.autocomplete {
  position:absolute;
  width:250px;
  background-color:white;
  border:1px solid #888;
  margin:0;
  padding:0;
}
div.autocomplete ul {
  list-style-type:none;
  margin:0;
  padding:0;
}
div.autocomplete ul li.selected { background-color: #0cf}
div.autocomplete ul li {
  list-style-type:none;
  display:block;
  margin:0;
  padding:2px;
  height:32px;
  cursor:pointer;
}
</style>
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
Professor: <input type = "text" id = "prof" name = "prof" /><br />
<div id = "prof_auto" class="autocomplete"></div>
<script type="text/javascript">
new Ajax.Autocompleter('prof','prof_auto','<?PHP echo site_url('search/professor_autocomplete')?>');
</script>


<!-- Autocomplete for this too -->
Subject: <input type = "text" id = "subj" name = "subj" /><br />
<div id = "subj_auto" class="autocomplete"></div>
<script type="text/javascript">
new Ajax.Autocompleter('subj','subj_auto','<?PHP echo site_url('search/subject_autocomplete')?>');
</script>

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
