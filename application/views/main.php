<html>
<!-- Author: Brian Corzo -->
<head>
<title>Schedule Builder 2.0</title>

<link rel="stylesheet" type="text/css" href="<?PHP echo str_replace('index.php/', '', site_url('stylesheets/style.css'))?>" />
<link rel="stylesheet" type="text/css" href="<?PHP echo str_replace('index.php/', '', site_url('stylesheets/prototip.css'))?>" />

<script type="text/javascript" src="<?PHP echo str_replace('index.php/','',site_url('javascripts/prototype.js'))?>"></script>
<script type="text/javascript" src="<?PHP echo str_replace('index.php/','',site_url('javascripts/scriptaculous.js'))?>"></script>
<script type="text/javascript" src="<?PHP echo str_replace('index.php/','',site_url('javascripts/prototip.js'))?>"></script>
<script type="text/javascript">
var last_action = null;
var last_action_params = null;
window.fbAsyncInit = function() {
FB.init({
  appId      : '121479874650613', // App ID
  channelUrl : '<?PHP echo site_url('schedule/channel')?>', // Channel File
  status     : true, // check login status
  cookie     : true, // enable cookies to allow the server to access the session
  xfbml      : true  // parse XFBML
});
};
// Load the SDK Asynchronously
(function(d){
 var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
 if (d.getElementById(id)) {return;}
 js = d.createElement('script'); js.id = id; js.async = true;
 js.src = "//connect.facebook.net/en_US/all.js";
 ref.parentNode.insertBefore(js, ref);
}(document));
function submit_form()
{
	if($('bSearch').visible())
	{
		url = '<?PHP echo site_url('search/basic_search')?>';
		new Ajax.Updater('full_search_results',url,{postBody:$('basic_search').serialize(),onComplete:show_undo});
	}
	else
	{
		url = '<?PHP echo site_url('search/advanced_search')?>';
		new Ajax.Updater('full_search_results',url,{postBody:$('search_form').serialize(),onComplete:show_undo});
	}
}
function add_course(myform)
{
	url = '<?PHP echo site_url('schedule/add_course')?>';
	last_action = 'add';
	last_action_params = myform.serialize();
	new Ajax.Request(url,{postBody:last_action_params,onSuccess:submit_form});
}
function remove_course(course_id)
{
	url = '<?PHP echo site_url('schedule/remove_course')?>';
	last_action = 'remove';
	last_action_params = 'call_number='+course_id;
	new Ajax.Request(url,{postBody:last_action_params,onSuccess:submit_form});
}
function show_schedule_grid()
{
	url = '<?PHP echo site_url('schedule/show_schedule_grid')?>';
	new Ajax.Updater('main_panel',url);
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
function add_subject(subject)
{
	var code = subject.title;
	var name = subject.innerHTML;
	
	var li = document.createElement('li');
	var hidden_input = document.createElement('input');
	var delete_link = document.createElement('a');
	
	hidden_input.name = 'subjects[]';
	hidden_input.type = 'hidden';
	hidden_input.value = code;
	
	delete_link.href = 'javascript:void(0);';
	delete_link.onclick = function(){ this.parentNode.remove(); };
	delete_link.innerHTML = '(X)';
	
	li.innerHTML = name + ' ';
	li.appendChild(hidden_input);
	li.appendChild(delete_link);
	
	$('subj_list').appendChild(li);
}
function save_schedule()
{
	url = '<?PHP echo site_url('schedule/save_schedule')?>';
	new Ajax.Request(url,{postBody:$('save_sched').serialize(),onSuccess:refresh_list,
			onFailure:display_error});
}
function refresh_list(response)
{
	$('side_panel').innerHTML = response.responseText;
}
function display_error(response)
{
	alert(response.responseText);
}
function new_schedule()
{
	url = '<?PHP echo site_url('schedule/new_schedule')?>';
	new Ajax.Request(url,{onSuccess:submit_form});

}
function show_undo()
{
	if(last_action == 'add')
	{
		if(last_action_params.indexOf('conflicting_call') > -1)
		{
			$('undo_box').innerHTML = 'Class added (conflicting class or classes removed). ';
		}
		else
		{
			$('undo_box').innerHTML = 'Class added. ';
		}
	}
	else if(last_action == 'remove')
	{
		$('undo_box').innerHTML = 'Class removed. ';
	}
	if(last_action != null)
	{
		var undo_link = document.createElement('a');
		undo_link.href = 'javascript:undo()';
		undo_link.innerHTML = 'Undo';
		$('undo_box').appendChild(undo_link);
	}
	else
	{
		$('undo_box').innerHTML = '';
	}
}
function undo()
{
	if(last_action == 'add')
	{
		url = '<?PHP echo site_url('schedule/unadd_course')?>';	
	}
	else if(last_action == 'remove')
	{
		url = '<?PHP echo site_url('schedule/add_course')?>';
	}
	if(last_action != null)
	{
		new Ajax.Request(url,{postBody:last_action_params,onSuccess:clear_and_submit_form});
	}
}
function clear_and_submit_form()
{
	last_action = null;
	last_action_params = null;
	submit_form();
}
<?PHP if(isset($load_schedule)):?>
function start_edit()
{
	url = '<?PHP echo site_url('schedule/request_access/'.$result->schedule->schedule_id)?>';
	new Ajax.Request(url,{onSuccess:confirm_edit});	
}
function confirm_edit()
{
	alert('A message has been sent to the email this schedule was saved with. Please click the link in that email to begin editing');
}
<?PHP endif;?>
function share_on_facebook()
{
	 FB.login(function(response) {
		if (response.status === 'connected') 
		{
			var token = response.authResponse.accessToken;
			url = '<?PHP echo site_url('schedule/share_on_facebook/')?>';
			new Ajax.Request(url,{postBody:'token='+token,onSuccess:function(){alert('Schedule shared on your timeline.');},onFailure:display_error});
		}
	 }, {scope: 'publish_stream'});
}
function submit_semester(sem, year)
{
	$('basic_year').value = year;
	$('basic_semester').value = sem;
	
	$('advanced_year').value = year;
	$('advanced_semester').value = sem;

	$('sem_select').toggle();
	$('search').toggle();
}
function toggle_sem_select()
{
	$('search').toggle();
	$('sem_select').toggle();
	new_schedule();
	$('full_search_results').style.visibility = "hidden";
}
function show_search_results()
{
	$('full_search_results').style.visibility = "visible";
}
</script>
</head>

<body>
<div id="fb-root">
<!-- The JS SDK requires the fb-root element in order to load properly. -->
</div>
<!--
<div id = "header"><img height = "125" src = "<?PHP echo str_replace('index.php/','',site_url('images/njitlogo.jpg'))?>">Schedule Builder 2.0</div> -->

<div id = "header">
<img style = "float:left" src = "<?PHP echo str_replace('index.php/','',site_url('images/njit_logo.gif'))?>">
Schedule Builder
</div>

<div id = "sem_select" style = "display: block; float:left; float:bottom;"">
<form method = "post" id = "semester">
<b>Select a semester:</b> <br />
<?PHP
	$date = getdate(date("U"));
	$year = $date['year'];
	//Spring
	if ($date['mon'] >= 1 && $date['mon'] <= 5)
	{
		echo '<input type = "radio" name = "semester" onclick = "submit_semester(\'spring\', \''.$year.'\')" id = "spring" value = "S" />SPRING '.$year.'<br />
				<input type = "radio" name = "semester" onclick = "submit_semester(\'summer\', \''.$year.'\')" id = "spring" value = "SM" />SUMMER '.$year.'<br />';
		echo '<input type = "radio" name = "semester" onclick = "submit_semester(\'fall\', \''.$year.'\')" id = "spring" value = "F" />FALL '.$year.'<br />';
	}
	//Summer
	else if ($date['mon'] >= 6 && $date['mon'] <= 9)
	{
		echo '<input type = "radio" name = "semester" onclick = "submit_semester(\'summer\', \''.$year.'\')" id = "SUMMER" value = "SM" />SUMMER '.$year.'<br />
				<input type = "radio" name = "semester" onclick = "submit_semester(\'fall\', \''.$year.'\')" id = "FALL" value = "F" />FALL '.$year.'<br />';
		$year++;
		echo '<input type = "radio" name = "semester" onclick = "submit_semester(\'spring\', \''.$year.'\')" id = "SPRING" value = "S" />SPRING '.$year.'<br />';
	}
	//Fall
	else
	{
		echo '<input type = "radio" name = "semester" onclick = "submit_semester(\'fall\', \''.$year.'\')" id = "FALL" value = "F" />FALL '.$year.'<br />';
		$year++;
		echo '<input type = "radio" name = "semester" onclick = "submit_semester(\'spring\', \''.$year.'\')" id = "SPRING" value = "S" />SPRING '.$year.'<br />
			<input type = "radio" name = "semester" onclick = "submit_semester(\'summer\', \''.$year.'\')" id = "SUMMER" value = "SM" />SUMMER '.$year.'<br />';
	}
?>
</form>
</div>


<div id = "search" style = "display: none">
<?PHP if(!isset($load_schedule) || $result->schedule->can_edit):?>
<!-- Basic Search Form -->
<div id = "bSearch" style = "display:block">
<form method = "post" action = "javascript:submit_form()" id = "basic_search">
<b>Basic Search</b>&nbsp;&nbsp;&nbsp;<a href="javascript:toggle_sem_select();">Back to Semester Selection</a>
<input type = "hidden" id = "basic_year" name = "year" value = "" />
<input type = "hidden" id = "basic_semester" name = "semester" value = "" />
<table style = "border-spacing: 25px 0px">
<tr>
<td>
<b>Keyword:</b> <input type = "text" id = "keyword" class = "rounded_corners" name = "keyword" /><br />
<script type = "text/javascript">new Tip($('keyword'), 'Ex: Roadmap to Computing, Theodore Nicholson, CS114');</script>
<br />
<b>Exclude:</b> &nbsp&nbsp<input type = "text" id = "exclude" class = "rounded_corners" name = "exclude" /><br />
<script type = "text/javascript">new Tip('exclude', 'Enter keyword to be excluded from search');</script>
</td>
<td>
<b>Day Restriction</b>
<br />
<input type = "radio" name = "day" id = "Radio1" value = "default" checked />I don't care<br />
<input type = "radio" name = "day" id = "Radio2" value = "nMon" />No Mondays<br />
<input type = "radio" name = "day" id = "Radio3" value = "nFri" />No Fridays<br />
</td>
<td>
<br />
<b>Time Restriction</b>
<br />
<input type = "radio" name = "time" value = "default" checked />I don't care<br />
<input type = "radio" name = "time" value = "1" />No 8:30am classes<br />
<input type = "radio" name = "time" value = "2" />No classes before 11:30am<br />
<input type = "radio" name = "time" value = "3" />Show only night classes<br />
</td>
<td>
<br />
<b>Course Level</b>
<br />
<input type = "radio" name = "course_level" value = "default" checked />I don't care<br />
<input type = "radio" id = "a" name = "course_level" value = "1" />Lower<br />
<input type = "radio" id = "b" name = "course_level" value = "2" />Upper<br />
<input type = "radio" id = "c" name = "course_level" value = "3" />Graduate<br />
<script type = "text/javascript">new Tip('a', '100/200 level courses');
new Tip('b', '300/400 level courses');
new Tip('c', 'Graduate level courses');</script>
</td>
<td style = "text-align: center">
<input type = "checkbox" name = "show_open_sections" value = "default" checked />Only show open sections
<br />
<br />
<input type = "submit" onclick = "javascript:show_search_results();" value = "Search" />
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
<input type = "hidden" id = "advanced_year" name = "year" value = "" />
<input type = "hidden" id = "advanced_semester" name = "semester" value = "" />
<b>Advanced Search</b>&nbsp;&nbsp;&nbsp;<a href="javascript:toggle_sem_select();">Back to Semester Selection</a>
<table style = "border-spacing: 25px 5px">
<tr>

<td>
<b>Professor:<b> <input type = "text" id = "prof" class = "rounded_corners" name = "prof" /><br />
<div id = "prof_auto" class="autocomplete"></div>
<script type="text/javascript">
new Ajax.Autocompleter('prof','prof_auto','<?PHP echo site_url('search/professor_autocomplete')?>');
new Tip('prof', 'Ex: Theodore Nicholson, Levy, John');
</script>
<br />
<b>Subject:</b> &nbsp&nbsp&nbsp&nbsp&nbsp<input type = "text" id = "subj" class = "rounded_corners" name = "subj" /><br />
<div id = "subj_auto" class="autocomplete"></div>
<ul id = "subj_list"></ul>
<script type="text/javascript">
new Ajax.Autocompleter('subj','subj_auto','<?PHP echo site_url('search/subject_autocomplete')?>', {updateElement:add_subject});
new Tip('subj', 'Ex: Computer Science, Math');
</script>
</td>

<td>
<b>Specify a time range:</b><br />

Within: <select name = "start_time">
<option value = "08:30" selected>8:30am</option>
<option value = "10:00">10:00am</option>
<option value = "11:30">11:30am</option>
<option value = "13:00">1:00pm</option>
<option value = "14:30">2:30pm</option>
<option value = "16:00">4:00pm</option>
<option value = "18:00">6:00pm</option>
</select><br />
to: &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<select name = "end_time">
<option value = "08:30">8:30am</option>
<option value = "10:00">10:00am</option>
<option value = "11:30">11:30am</option>
<option value = "13:00">1:00pm</option>
<option value = "14:30">2:30pm</option>
<option value = "16:00">4:00pm</option>
<option value = "18:00">6:00pm</option>
<option value = "21:00">9:00pm</option>
<option value = "21:45" selected>9:45pm</option>
</select>
</td>

<td>
<br />
<br />
<b>Course Level</b>
<br />
<input type = "radio" name = "course_level" value = "default" checked />I don't care<br />
<input type = "radio" id = "d" name = "course_level" value = "1" />Lower<br />
<input type = "radio" id = "e" name = "course_level" value = "2" />Upper<br />
<input type = "radio" id = "f" name = "course_level" value = "3" />Graduate<br />
<script type = "text/javascript">new Tip('d', '100/200 level courses');
new Tip('e', '300/400 level courses');
new Tip('f', 'Graduate level courses');</script>
</td>

<td>
<b>Distance Learning</b>
<br />
<input type = "radio" name = "online" value = "default" checked />I don't care<br />
<input type = "radio" name = "online" value = "show_online" />Only Show Online Classes<br />
<input type = "radio" name = "online" value = "hide_online" />Don't Show Online Classes<br />
</td>
</tr>

<tr>

<td></td>

<td>
<b>Day Restriction</b>
<br />
<input type = "radio" name = "day" value = "default" checked />I don't care<br />
<input type = "radio" name = "day" value = "nMon" />No Mondays<br />
<input type = "radio" name = "day" value = "nFri" />No Fridays<br />
</td>

<td>
<b>Honors</b>
<br />
<input type = "radio" name = "honors" value = "default" checked />I don't care<br />
<input type = "radio" name = "honors" value = "show_honors" />Only Show Honors Classes<br />
<input type = "radio" name = "honors" value = "hide_honors" />Don't Show Honors Classes<br />
</td>

<td style = "text-align:center">
<input type = "checkbox" name = "show_open_sections" value = "default" checked />Only show open sections
<br />
<br />
<input type = "submit" onclick = "javascript:show_search_results();" value = "Search" />
<br />
<a href="javascript:toggle_search();">Return to Basic Search</a>
</td>
</tr>
</table>
</form>
</div>
<div style = "background-color:#00FF66; color:#000000; font-weight:bold; text-align:center;" id="undo_box"></div>
<!-- End Advanced Search Form -->
</div>
<?PHP else:?>
<div style="background-color:#fcc">Viewing a saved schedule in read-only mode. If you are the owner, click <a href="javascript:start_edit()">here</a> to edit.</div><br />
<?PHP endif;?>

<div id="full_search_results">
<?PHP if(isset($load_schedule)):
	$this->load->view('panels',$result);
endif;?>
</div>

</body>
</html>
