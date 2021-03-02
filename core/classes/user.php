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

	public function register($email, $screenName, $password){
		$stmt = $this->pdo->prepare("INSERT INTO `users` (`email`, `screenName`, `password`, `profileImage`,`profileCover`) VALUES (:email, :screenName, :password, 'assets/images/defaultProfileImage.png', 'assets/images/defaultCoverImage.png')");

		// hash the password
    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':screenName', $screenName, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hash_password, PDO::PARAM_STR);

    $stmt->execute();

    $user_id = $this->pdo->lastInsertId();

    $_SESSION['user_id'] = $user_id;
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

	public function create($table, $fields = array()){
		$columns = implode(',', array_keys($fields));
		$values = ':'.implode(', :', array_keys($fields));
		$sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";

		if($stmt = $this->pdo->prepare($sql)){
			foreach($fields as $key => $data){
				$stmt->bindValue(':'.$key, $data);
			}
			$stmt->execute();
			// $_SESSION['user_id'] = $this->pdo->lastInsertId();
			return $this->pdo->lastInsertId();
		}
	}

	public function checkEmail($email){
		$stmt = $this->pdo->prepare("SELECT `email` FROM `users` WHERE `email` = :email");
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->execute();

		if($stmt->rowCount() != 0){
			return true;
		}

		return false;
	}
}