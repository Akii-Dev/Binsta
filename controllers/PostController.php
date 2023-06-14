<?php

class PostController extends BaseController
{
	public function randStr()
	{
		return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 10);
	}

	public function index()
	{
		$this->authorizeUser();
		$post = R::findAll('post', 'visibility = ?', ['public']);

		$post = array_reverse($post);

		$reaction = R::findAll('reaction');
		$reaction = array_reverse($reaction);

		displayTemplate("/posts/index.twig", ["post" => $post, "reaction" => $reaction]);
	}

	public function replyPost()
	{
		$this->authorizeUser();
		$trimmedReaction = trim($_POST["comment"]);
		if ($trimmedReaction !== "") {
			$post  = R::findOne('post', ' url = ? ', [$_GET["id"]]);

			$reaction = R::dispense('reaction');
			$reaction->user_id = $_SESSION["id"];
			$reaction->post_id = $post->id;
			$reaction->content = $trimmedReaction;

			R::store($reaction);
		}

		header("Location: " . $_SERVER["HTTP_REFERER"]);
	}

	public function likePost()
	{
		$this->authorizeUser();

		if ($_POST["status"] == "like") {
			$like = R::dispense('like');
			$like->user_id = $_SESSION["id"];
			$like->post_id = $_GET["id"];
			R::store($like);
		} else {
			$remove = R::findOne('like', ' user_id = ? AND post_id = ? ', [
				$_SESSION["id"],
				$_GET["id"]
			]);

			R::trash($remove);
		}
		header("Location: " . $_SERVER["HTTP_REFERER"]);
	}

	public function show()
	{
		$this->authorizeUser();
		$error = null;
		if (isset($_POST["error"]) && $_POST["error"] !== "") {
			$error = $_POST["error"];
		}

		if (!isset($_GET['id'])) {
			error(404, "No post url specified");
		} else {
			$id = $_GET['id'];
			foreach (R::findAll('post') as $key => $post) {
				if ($post["url"] == $id) {
					$link = $key;

					$post  = R::findOne('post', ' url = ? ', [$id]);
					$reaction  = R::findAll('reaction', ' post_id = ? ', [$post->id]);
					$reaction = array_reverse($reaction);

					displayTemplate("/posts/show.twig", ["post" => $post, "reaction" => $reaction, "error" => $error]);
				}
			}
			if (!isset($link)) {
				error(404, "No post with url " . $id . " found");
			}
		}
	}

	public function edit()
	{
		$this->authorizeUser();
		$error = null;
		if (isset($_POST["error"]) && $_POST["error"] !== "") {
			$error = $_POST["error"];
		}
		if (!isset($_GET['id'])) {
			error(404, "No post url specified");
		} else {
			$id = $_GET['id'];
			foreach (R::findAll('post') as $key => $post) {
				if ($post["url"] == $id) {
					$link = $key;

					$post  = R::findOne('post', ' url = ? ', [$id]);

					if ($_SESSION["id"] == $post->user_id) {
						displayTemplate("/posts/edit.twig", ["post" => $post, "error" => $error]);
					} else {
						$_POST["error"] = "You are not authorized to edit this post";
						$this->show();
						exit();
					}
				}
			}
			if (!isset($link)) {
				error(404, "No post with url " . $id . " found");
			}
		}
	}

	public function editPost()
	{
		$this->authorizeUser();
		$post  = R::findOne('post', ' url = ? ', [$_GET["id"]]);

		if ($_SESSION["id"] == $post->user_id) {
			$trimmedPostContent = trim($_POST["textarea"]);

			if ($trimmedPostContent !== "") {
				$post->content = $_POST["textarea"];
				$post->description = trim($_POST["description"]);
				$post->visibility = $_POST["visibility"];

				R::store($post);
			} else {
				$_POST["error"] = "Post content is not allowed to only contain blank characters";
				$this->edit();
				exit();
			}
		} else {
			$_POST["error"] = "You are not authorized to edit this post";
			$this->show();
			exit();
		}

		header("Location: ../post/show?id=" . $_GET["id"] . "");
	}

	public function create()
	{
		$this->authorizeUser();
		$error = null;
		if (isset($_POST["error"]) && $_POST["error"] !== "") {
			$error = $_POST["error"];
		}

		displayTemplate("/posts/create.twig", $error);
	}

	public function createPost()
	{
		$this->authorizeUser();

		if (isset($_POST["textarea"]) && $_POST["textarea"] !== "") {
			$trimmedPostContent = trim($_POST["textarea"]);

			if ($trimmedPostContent !== "") {
				$url = $this->randStr();

				$post = R::dispense('post');
				$post->user_id = ($_SESSION["id"]);

				$post->content = $trimmedPostContent;
				$post->language = $_POST["language"];
				$post->visibility = $_POST["visibility"];
				$post->description = trim($_POST["description"]);
				$post->url = $url;

				R::store($post);
				header("Location: ../post/show?id=" . $url . "");
			} else {
				$_POST["error"] = "Post content is not allowed to only contain blank characters";
				$this->create();
				exit();
			}
		} else {
			$_POST["error"] = "Post cannot be empty";
			$this->create();
			exit();
		}
	}

	public function delete()
	{
		$this->authorizeUser();

		$post  = R::findOne('post', ' url = ? ', [$_GET["id"]]);
		$likes  = R::findAll('like', ' post_id = ? ', [$post->id]);
		$reactions  = R::findAll('reaction', ' post_id = ? ', [$post->id]);


		if ($_SESSION["id"] == $post->user_id) {
			foreach ($likes as $like) {
				R::trash($like);
			}

			foreach ($reactions as $reaction) {
				R::trash($reaction);
			}
			R::trash($post);
		}

		header("Location: ../");
	}
}
