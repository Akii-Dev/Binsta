<?php

require('rb.php');
require('dbconnection.php');

function randStr()
{
	return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 10);
}

$countPostsAdded = 10;
$countCommentsAdded = 20;
$countLikesAdded = 0;
$users = [
	[
		'name'  => 'future-tech-leader',
		'password' => 'password',
	],
	[
		'name'  => 'Lorem',
		'password' => '12345678',
	],
	[
		'name'  => 'president',
		'password' => 'loremipsumdolor',
	],
	[
		'name'  => 'mark',
		'password' => 'h28X54Gds',
	],
	[
		'name'  => 'me',
		'password' => 'SuperSecret2',
	],
	[
		'name'  => 'person',
		'password' => 'hello271',
	],
];

$code = [
	[
		'language'  => 'html',
		'content'  => '<form action="/action_page.php">
		<label for="fname">First name:</label><br>
		<input type="text" id="fname" name="fname" value="John"><br>
		<label for="lname">Last name:</label><br>
		<input type="text" id="lname" name="lname" value="Doe"><br><br>
		<input type="submit" value="Submit">
	  </form> ',
	],
	[
		'language'  => 'css',
		'content'  => '.p1 {
			font-family: "Times New Roman", Times, serif;
		  }
		  
		  .p2 {
			font-family: Arial, Helvetica, sans-serif;
		  }
		  
		  .p3 {
			font-family: "Lucida Console", "Courier New", monospace;
		  }',
	],
	[
		'language'  => 'javascript',
		'content'  => 'let text = "Apple, Banana, Kiwi";
		let part = text.slice(7, 13);',
	],
	[
		'language'  => 'php',
		'content'  => '<?php
		echo "Hello World!";
		?>',
	],
];

$pictures = [
	"default-pfp.png",
	"boom.png",
	"astronaut.png",
	"mountain.png",
	"borzoi.png",
	"edgy.png",
];

R::nuke();

foreach ($users as $user) {
	$userColumn = R::dispense('user');
	$userColumn->name = $user["name"];
	$userColumn->picture = $pictures[rand(0, count($pictures) - 1)];
	$userColumn->password = password_hash($user["password"], PASSWORD_ARGON2I);
	$userColumn->description = file_get_contents('https://loripsum.net/api/1/short/plaintext');

	$id = R::store($userColumn);
}
echo count($users) . " users inserted" . PHP_EOL;


for ($i = 0; $i <= $countPostsAdded; $i++) {
	$postIndex = rand(0, 3);

	$post = R::dispense('post');
	$post->user_id = rand(1, 6);
	$post->content = $code[$postIndex]["content"];
	$post->language = $code[$postIndex]["language"];
	$post->visibility = "public";
	$post->description = file_get_contents('https://loripsum.net/api/2/short/plaintext');
	$post->url = randStr();
	$id = R::store($post);
}
echo $countPostsAdded . " posts inserted" . PHP_EOL;

for ($i = 0; $i <= $countCommentsAdded; $i++) {
	$reaction = R::dispense('reaction');
	$reaction->user_id = rand(1, 6);
	$reaction->post_id = rand(1, 11);
	$reaction->content = file_get_contents('https://loripsum.net/api/1/veryshort/plaintext');
	$id = R::store($reaction);
}
echo $countCommentsAdded . " comments inserted" . PHP_EOL;

for ($i = 1; $i <= count($users); $i++) {
	for ($j = 1; $j <= $countPostsAdded; $j++) {
		if (rand(0, 1)) {
			$like = R::dispense('like');
			$like->user_id = $i;
			$like->post_id = $j;
			$id = R::store($like);

			$countLikesAdded++;
		}
	}
}

echo $countLikesAdded . " likes inserted" . PHP_EOL;

R::close();
