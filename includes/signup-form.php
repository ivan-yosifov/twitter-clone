<?php
if(isset($_POST['signup'])){
	$screenName = $_POST['screenName'];
	$password = $_POST['password'];
	$email = $_POST['email'];
	$error = '';

	if(empty($screenName) or empty($password) or empty($email)){
		$error = 'All fields are required';
	}else{
		$email = $getFromU->checkInput($email);
		$password = $getFromU->checkInput($password);
		$screenName = $getFromU->checkInput($screenName);

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$error = 'Invalid email format';
		}else if(strlen($screenName) > 20){
			$error = 'Name must be between 5 and 20 characters';
		}else if(strlen($password) < 5){
			$error = 'Password is too short';
		}else{
			if($getFromU->checkEmail($email) === true){
				$error = 'Email is already in use';
			}else{
				$getFromU->create('users', array('email' => $email, 'screenName' => $screenName, 'password' => password_hash($password, PASSWORD_DEFAULT), 'profileImage' => 'assets/images/defaultProfileImage.png', 'profileCover' => 'assets/images/defaultCoverImage.png'));
				header('Location: includes/signup.php?step=1');
				exit();
			}
		}
	}

}
?>
<form method="post" novalidate="">
<div class="signup-div"> 
	<h3>Sign up </h3>
	<ul>
		<li>
		    <input type="text" value="<?php if(isset($screenName)) echo $screenName; ?>" name="screenName" placeholder="Full Name"/>
		</li>
		<li>
		    <input type="email" value="<?php if(isset($email)) echo $email; ?>" name="email" placeholder="Email"/>
		</li>
		<li>
			<input type="password" name="password" placeholder="Password"/>
		</li>
		<li>
			<input type="submit" name="signup" Value="Signup for Twitter">
		</li>
		<?php if(isset($error) && !empty($error)): ?>
	 <li class="error-li">
	  <div class="span-fp-error"><?php echo $error; ?></div>
	 </li> 
	<?php endif; ?>
	</ul>
	
</div>
</form>