<?php

class UserController extends BaseController
{
	public function checkAlreadyLoggedIn()
	{
		if (isset($_SESSION["id"])) {
			header("Location: ../");
			exit;
		}
	}

	public function login()
	{
		$this->checkAlreadyLoggedIn();
		$error = null;
		if (isset($_POST["error"]) && $_POST["error"] !== "") {
			$error = $_POST["error"];
		}
		displayTemplate("/user/login.twig", $error);
	}

	public function loginPost()
	{
		if (R::findOne("user", "name=?", array($_POST["name"])) == null) {
			$_POST["error"] = "Invalid username";
			$this->login();
			exit();
		} else {
			$user = R::findOne("user", "name=?", array($_POST["name"]));
			$password = $_POST["password"];
			$hash = $user->password;
			if (password_verify($password, $hash) == true) {
				$_SESSION["id"] = $user->id;
				header("Location: ../post");
			} else {
				$_POST["error"] = "Invalid username or password";
				$this->login();
				exit();
			}
		}
	}

	public function settings()
	{
		$this->authorizeUser();

		$passwordError = null;
		$nameError = null;
		$pictureError = null;

		if (isset($_POST["picture-error"]) && $_POST["picture-error"] !== "") {
			$pictureError = $_POST["picture-error"];
		}
		if (isset($_POST["name-error"]) && $_POST["name-error"] !== "") {
			$nameError = $_POST["name-error"];
		}
		if (isset($_POST["password-error"]) && $_POST["password-error"] !== "") {
			$passwordError = $_POST["password-error"];
		}
		displayTemplate(
			"/user/settings.twig",
			["users" => R::findAll('user'), "nameerror" => $nameError, "passworderror" => $passwordError, "pictureerror" => $pictureError]
		);
	}

	public function settingsPost()
	{

		$user = R::load('user', $_SESSION["id"]);

		$allUsers = R::findAll('user');
		$usernameAvailability = true;

		$trimmedUserName = trim($_POST["username"]);
		if ($trimmedUserName == "") {
			$_POST["name-error"] = "Username is not allowed to be empty";
			$usernameAvailability = false;
		}

		if ($user->name == $trimmedUserName) {
			$usernameAvailability = false;
		} else {
			foreach ($allUsers as $userFromArray) {
				if ($trimmedUserName == $userFromArray->name) {
					$_POST["name-error"] = "Username is already in use";

					$usernameAvailability = false;
				}
			}
		}

		if ($usernameAvailability == true) {
			$user->name = $_POST["username"];
		}

		$user->description = $_POST["description"];


		if ($_POST["remove-status"] == "true") {
			$user->picture = "default-pfp.png";
		} elseif (isset($_FILES["user-photo"]["name"]) && $_FILES["user-photo"]["name"] !== "") {
			$target_dir = "$_SERVER[DOCUMENT_ROOT]/images/";
			$target_file = $target_dir . "$user->id.png";

			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

			$check = getimagesize($_FILES["user-photo"]["tmp_name"]);
			if ($check !== false) {
				$uploadOk = 1;
			} else {
				$_POST["picture-error"] = "File must be an image";

				$uploadOk = 0;
			}

			if (
				$imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			) {
				$_POST["picture-error"] = "Sorry, only JPG, JPEG & PNG files are allowed.";
				$uploadOk = 0;
			}

			if ($uploadOk == 0) {
				$_POST["picture-error"] = "Sorry, your file could not be uploaded.";
			} else {
				if (move_uploaded_file($_FILES["user-photo"]["tmp_name"], $target_file)) {
					$user->picture = "$user->id.png";
				} else {
					$_POST["picture-error"] = "Sorry, there was an error uploading your file.";
				}
			}
		}
		R::store($user);
		$this->settings();
	}

	public function changePasswordPost()
	{
		$user = R::load('user', $_SESSION["id"]);
		$password = $_POST["current-password"];
		$hash = $user->password;
		if (password_verify($password, $hash) == true) {
			if ($_POST["new-password"] == $_POST["confirm-new-password"]) {
				$user->password = password_hash($_POST["new-password"], PASSWORD_ARGON2I);
				R::store($user);
				$this->settings();
			} else {
				$_POST["password-error"] = "Passwords don't match";
				$this->settings();
				exit();
			}
		} else {
			$_POST["password-error"] = "Current password incorrect";
			$this->settings();
			exit();
		}
	}

	public function logout()
	{
		session_destroy();
		header("Location: ../");
	}

	public function delete()
	{

		if ($_SESSION["id"] == $_GET["id"]) {
			$user = R::load('user', $_SESSION["id"]);
			$postsFromDeletedUser = R::findAll('post', ' user_id = ? ', [$user->id]);
			$commentsFromDeletedUser = R::findAll('reaction', ' user_id = ? ', [$user->id]);
			$likesFromDeletedUser = R::findAll('like', ' user_id = ? ', [$user->id]);

			foreach ($postsFromDeletedUser as $post) {
				$commentOnSelected = R::findAll('reaction', ' post_id = ? ', [$post->id]);
				$likesOnSelected = R::findAll('like', ' post_id = ? ', [$post->id]);
				R::trashAll($likesOnSelected);
				R::trashAll($commentOnSelected);
			}
			R::trashAll($postsFromDeletedUser);
			R::trashAll($commentsFromDeletedUser);
			R::trashAll($likesFromDeletedUser);
			R::trash($user);

			$this->logout();
		}
	}

	public function register()
	{
		$this->checkAlreadyLoggedIn();

		$error = null;
		if (isset($_POST["error"]) && $_POST["error"] !== "") {
			$error = $_POST["error"];
		}
		displayTemplate("/user/register.twig", $error);
	}

	public function registerPost()
	{
		$trimmedName = trim($_POST["name"]);
		if ($trimmedName == "") {
			$_POST["error"] = "Username is not allowed to be empty";
			$this->register();
			exit();
		}
		if ($_POST["password"] !== $_POST["confirm-password"]) {
			$_POST["error"] = "Passwords don't match";
			$this->register();
			exit();
		}
		if (strlen($_POST["password"]) < 8) {
			$_POST["error"] = "Password is too short!";
			$this->register();
			exit();
		}


		$allUsers = R::findAll('user');
		foreach ($allUsers as $user) {
			if ($trimmedName == $user->name) {
				$_POST["error"] = "Username is already in use";
				$this->register();
				exit();
			}
		}

		$user = R::dispense('user');
		$user->name = $trimmedName;
		$user->password = password_hash($_POST["password"], PASSWORD_ARGON2I);
		$user->picture = "default-pfp.png";

		R::store($user);
		$_SESSION["id"] = $user->id;
		header("Location: ../post");
	}

	public function users()
	{
		displayTemplate("/user/users.twig", R::findAll('user'));
	}

	public function about()
	{

		if (!isset($_GET['name'])) {
			error(404, "No username specified");
		} else {
			$id = $_GET['name'];
			foreach (R::findAll('user') as $key => $user) {
				if ($user["name"] == $id) {
					$link = $key;

					$user  = R::findOne('user', ' name = ? ', [$id]);

					$posts = R::find("post", "user_id = ? AND visibility = ? ", array($user->id, 'public '));

					displayTemplate("/user/about.twig", ["user" => $user, "post" => $posts]);
				}
			}
			if (!isset($link)) {
				error(404, "No user with name " . $id . " found");
			}
		}
	}
}
