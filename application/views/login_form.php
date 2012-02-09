<?PHP $this->load->helper('url');?>
<html>
<head>
<title>CS 490 Project -- Alpha</title>
</head>

<body>

<!-- User enters name in this form and submits -->
<form action = "<?PHP echo site_url('/login/do_login')?>" method = "post" />
<p>Enter your name: <input type = "text" name="username" /></p>
<input type = "submit" value = "Submit" />
</form>


</body>
</html>
