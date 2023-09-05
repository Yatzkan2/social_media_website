<?php
define('SERVERNAME', 'localhost');
define('USERNAME', 'root');
define('PASSWORD', '');
define('DBNAME', 'social');
define('USERS_TABLE_NAME', 'users');
define('POSTS_TABLE_NAME', 'posts');

define('USERS_COLUMNS', ['user_id int primary key', 'email varchar(255) unique', 'birthday date', 'active_user boolean']);
define('USERS_COL_NAMES', ['user_id', 'email', 'birthday', 'active_user']);

define('POSTS_COLUMNS', ['post_id int primary key', 'author_id int', 'title varchar(255)', 'content varchar(1000)', 'posted_date date', 'posted_time time', 'active_post boolean']);
define('POSTS_COL_NAMES', ['post_id', 'author_id', 'title', 'content', 'posted_date', 'posted_time', 'active_post']);

define('USERS_API_URL', 'https://jsonplaceholder.typicode.com/users/');
define('POSTS_API_URL', 'https://jsonplaceholder.typicode.com/posts/');
define('IMG_URL', 'https://cdn2.vectorstock.com/i/1000x1000/23/81/default-avatar-profile-icon-vector-18942381.jpg');
define('IMG_FOLDER_PATH', 'images/');

define("MONTHS_NAMES",['01' => 'January',
'02' => 'February',
'03' => 'March',
'04' => 'April',
'05' => 'May',
'06' => 'June',
'07' => 'July',
'08' => 'August',
'09' => 'September',
'10' => 'October',
'11' => 'November',
'12' => 'December'])
?>