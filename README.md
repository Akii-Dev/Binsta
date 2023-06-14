# **Binsta**

## Description

Binsta is a social media platform for creating and sharing code snippets.

## Features
* Share highlighted snippets
* Comments
* Liking posts
* Registration

## Requirements
* XAMPP (or another SQL admin tool)
* Composer
* Redbean

# Installation
1. Install composer in the root:
```
$ composer install
```

2. Import db.sql in your SQL admin tool.
3. Use an account with the following information:
```
hostname: localhost
username: username
password: password
```
 You can also use a different user. To do this change the credentials in "dbconnection.php"
* Run the file "seeder.php"
* Run the program through the "public" folder

## If the file "rb.php" doesn't work properly, follow these next steps and repeat from step 2.
---
* Install [Redbean](https://redbeanphp.com/index.php?p=/install) through the official website using the CLI.
If this doesn't work; download the file [rb.php](https://github.com/teppokoivula/RedBeanPHP/blob/master/RedBeanPHP/rb.php) online instead.
* Place "rb.php into the root of the repository"
---

# Usage
Once the program has been built. You can log in with a preexisting account or register a new one.

When logged in; you are able to create snippets, like and comment on other people's posts.

You can also choose to make your post private so it won't appear on any page. You can still find these posts if you have the URL.  
You can also edit or delete any post you create.

You are also able to edit your own account and delete it if desired.

