<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/registrationStyle.css">
    </head>
    <body>
        <div class="form-box">
<form class="form">
    <span class="title">Sign up</span>
    <span class="subtitle">Create a free account with your email.</span>

    <select class="input">
        <option value="" disabled selected>Sign up as</option>
        <option value="project_owner">Project Owner</option>
        <option value="project_applicant">Project Applicant</option>
    </select>
    <div class="form-container">
        <input type="text" class="input" placeholder="Full Name">
		<input type="email" class="input" placeholder="Email">
		<input type="password" class="input" placeholder="Password">
		<input type="password" class="input" placeholder="Confirm Password">
        <div class="gender-selection">
            <div>Select your gender </div>
        <label for="male">
            <input type="radio" name="gender" id="male" value="male"> Male
        </label>
        <label for="female">
            <input type="radio" name="gender" id="female" value="female"> Female 
        </label>
        </div>
    </div>
    <button>Sign up</button>
</form>
<div class="form-section">
  <p>Have an account? <a href="login.php">Log in</a> </p>
</div>
</div>
    </body>
</html>