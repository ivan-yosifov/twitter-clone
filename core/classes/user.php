<?php
error_reporting( E_ALL );
class User {
	protected $pdo;

	function __construct($pdo){
		$this->pdo = $pdo;
	}

	public function checkInput($input){
		$input = htmlspecialchars($input);
		$input = trim($input);
		$input = stripslashes($input);

		return $input;
	}

	public function login($email, $password){
		$stmt = $this->pdo->prepare("SELECT `user_id`, `password` FROM users WHERE `email` = :email");
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->execute();

		$user = $stmt->fetch(PDO::FETCH_OBJ);
		$count = $stmt->rowCount();

		if($count > 0){
			$hash_password = $user->password;	
			if(!password_verify($password, $user->password)){
				return false;
			}else{
				$_SESSION['user_id'] = $user->user_id;
				header('Location: home.php');
				exit();
			}
		}else{
			return false;
		}
		
	}

	public function userData($user_id){
		$stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `user_id` = :user_id");
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function logout(){
		$_SESSION = array();
		session_destroy();
		header('Location: ../index.php');
		exit();
	}
}