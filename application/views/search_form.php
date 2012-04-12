<html>
<!-- Author: Brian Corzo -->
<head>
<title>Search classes v1.0</title>


<link rel="stylesheet" type="text/css" href="<?PHP echo str_replace('index.php/', '', site_url('stylesheets/style.css'))?>" />
<link rel="stylesheet" type="text/css" href="<?PHP echo str_replace('index.php/', '', site_url('stylesheets/prototip.css'))?>" />

<script type="text/javascript" src="<?PHP echo str_replace('index.php/','',site_url('javascripts/prototype.js'))?>"></script>
<script type="text/javascript" src="<?PHP echo str_replace('index.php/','',site_url('javascripts/scriptaculous.js'))?>"></script>
<script type="text/javascript" src="<?PHP echo str_replace('index.php/','',site_url('javascripts/prototip.js'))?>"></script>
<script type="text/javascript">
function submit_form()
{
	if($('bSearch').visible)
	{
		url = '<?PHP echo site_url('search/basic_search')?>';
		new Ajax.Updater('full_search_results',url,{postBody:$('basic_search').serialize()});
	}
	else
	{
		url = '<?PHP echo site_url('search/advanced_search')?>';
		new Ajax.Updater('full_search_results',url,{postBody:$('search_form').serialize()});
	}
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
function toggle_search()
{
	$("bSearch").toggle();
	$("aSearch").toggle();
}
function show_email()
{
	$('email').toggle();
}
</script>
</head>

<body>


<div id = "header">Schedule Builder 2</div>
<br />

<!-- Basic Search Form -->
<div id = "bSearch" style = "display:block">
<form method = "post" action = "javascript:submit_form()" id = "basic_search">
Basic Search
<table border = "1">
<tr>
<td>
Keyword: <input type = "text" id = "keyword" name = "keyword" /><br />
<script type = "text/javascript">new Tip(keyword, 'Ex: Roadmap to Computing, Theodore Nicholson, CS114');</script>
<br />
Exclude: &nbsp<input type = "text" id = "exclude" name = "exclude" /><br />
<script type = "text/javascript">new Tip(exclude, 'Enter keyword to be excluded from search');</script>
</td>
<td>
Day Restriction
<br />
<input type = "radio" name = "day" value = "default" />Whatever<br />
<input type = "radio" name = "day" value = "nMon" />No Mondays<br />
<input type = "radio" name = "day" value = "nFri" />No Fridays<br />
</td>
<td>
<br />
Time Restriction
<br />
<input type = "radio" name = "time" value = "default" />I don't care<br />
<input type = "radio" name = "time" value = "1" />8:30am<br />
<input type = "radio" name = "time" value = "2" />Before 10am<br />
<input type = "radio" name = "time" value = "3" />Night classes (6pm)<br />
</td>
<td>
<br />
Course Level
<br />
<input type = "radio" name = "course_level" value = "default" />I don't care<br />
<input type = "radio" id = "a" name = "course_level" value = "1" />Lower<br />
<input type = "radio" id = "b" name = "course_level" value = "2" />Upper<br />
<input type = "radio" id = "c" name = "course_level" value = "3" />Graduate<br />
<script type = "text/javascript">new Tip(a, '100/200 level courses');
new Tip(b, '300/400 level courses');
new Tip(c, 'Graduate level courses');</script>
</td>
<td>
<input type = "checkbox" name = "show_open_sections" value = "default" checked />Only show open sections
<br />
<br />
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type = "submit" value = "Search" />
<br />
<a href="javascript:toggle_search();">Advanced Search</a>
</td>
</tr>
</table>
</form>
</div>
<!-- End Basic Search Form -->

<!-- Advanced Search Form -->
<div id="aSearch" style="display:none">
<form method = "post" action = "javascript:submit_form()" id = "search_form">
Advanced Search
<table border = "1">
<tr>
<td>
Professor: <input type = "text" id = "prof" name = "prof" /><br />
<div id = "prof_auto" class="autocomplete"></div>
<script type="text/javascript">
new Ajax.Autocompleter('prof','prof_auto','<?PHP echo site_url('search/professor_autocomplete')?>');
new Tip(prof, 'Ex: Theodore Nicholson, Levy, John');
</script>
<br />
Subject: &nbsp&nbsp&nbsp&nbsp<input type = "text" id = "subj" name = "subj" /><br />
<div id = "subj_auto" class="autocomplete"></div>
<script type="text/javascript">
new Ajax.Autocompleter('subj','subj_auto','<?PHP echo site_url('search/subject_autocomplete')?>');
new Tip(subj, 'Ex: Computer Science, Math');
</script>
<br />
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
Day Restriction
<br />
<input type = "radio" name = "day" value = "default" />I don't care<br />
<input type = "radio" name = "day" value = "nMon" />No Mondays<br />
<input type = "radio" name = "day" value = "nFri" />No Fridays<br />
</td>
<td>
<br />
Time Restriction
<br />
<input type = "radio" name = "time" value = "default" />I don't care<br />
<input type = "radio" name = "time" value = "1" />8:30am<br />
<input type = "radio" name = "time" value = "2" />Before 10am<br />
<input type = "radio" name = "time" value = "3" />Night classes (6pm)<br />
</td>
<td>
<br /><br />
Course Level
<br />
<input type = "radio" name = "course_level" value = "default" />I don't care<br />
<input type = "radio" id = "a" name = "course_level" value = "1" />Lower<br />
<input type = "radio" id = "b" name = "course_level" value = "2" />Upper<br />
<input type = "radio" id = "c" name = "course_level" value = "3" />Graduate<br />
<script type = "text/javascript">new Tip(a, '100/200 level courses');
new Tip(b, '300/400 level courses');
new Tip(c, 'Graduate level courses');</script>
</td>
</tr>
<tr>
<td></td>
<td>
Distance Learning
<br />
<input type = "radio" name = "online" value = "default" />I don't care<br />
<input type = "radio" name = "online" value = "show_online" />Show Online Classes<br />
<input type = "radio" name = "online" value = "hide_online" />Don't Show Online Classes<br />
</td>
<td>
Honors
<br />
<input type = "radio" name = "honors" value = "default" />I don't care<br />
<input type = "radio" name = "honors" value = "show_honors" />Show Honors Classes<br />
<input type = "radio" name = "honors" value = "hide_honors" />Don't Show Honors Classes<br />
</td>
<td>
<input type = "checkbox" name = "show_open_sections" value = "default" checked />Only show open sections
<br />
<br />
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type = "submit" value = "Search" />
<br />
<a href="javascript:toggle_search();">Return to Basic Search</a>
</td>
</tr>
</table>
</form>
</div>
<!-- End Advanced Search Form -->

<div id="full_search_results"></div>

</body>
</html>
