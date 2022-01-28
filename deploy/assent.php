<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title></title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


</head>
<body>
 <div class="container">
<h1>Getting Started</h1>
<p class ="lead">To get started, please "sign" the form below by typing your name. Also, fill out a name that you want us to call you.</p>


<form action="process_form.php" method="post">
<p class ="lead">I, understand that my parents (mom and dad)/guardian have/has given permission (said it’s okay) for me to take part in a design team where I will design new things for children with other kids and adults.
<p class ="lead">I am taking part because I want to.  I have been told that I can stop at any time I want to and nothing will happen to me if I want to stop. 
<p class ="lead">
Your name: <input name="assent_sign" id="assent_sign"  required>
<p class ="lead">Your guardian's email: <input name="parent_email" id="parent_email" required>
<p class ="lead">What do you want us to call you? (Don't use your full name!) <input name="user_id" id="user_id" required>

<p><input type="submit" value="Continue to KidsTeam Online" class="btn btn-primary">
</form>
</div>
</body>
</html>