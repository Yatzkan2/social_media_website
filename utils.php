<?php
include_once("./config.php");
class Database {
    private $server;
    private $username;
    private $password;
    private $dbname;
    private $connection;
    public function __construct($server, $username, $password, $dbname) {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->connect();
    }
    public static function create_db($dbname, $server, $username, $password="") {
        $conn=mysqli_connect($server, $username, $password);
        if(!$conn) {
            die("Failed connecting to server '$server'. <br>".mysqli_connect_error()); 
        } 
        //database creation query
        $query = "create database $dbname"; //error handling behaves different than the manual instructions.
        try {
            mysqli_query($conn, $query);
            echo "database $dbname has created. <br>";
        } 
        catch (mysqli_sql_exception $e) {
            echo "Failed creating database $dbname: <br>$e";
        }
        mysqli_close($conn);
    }
    public function create_table($tablename, $columns=[]) {
        //create table query generation
        $query = "create table $tablename ( ";
        foreach($columns as $col) {
            $query .= "$col, ";
        }
        $query = substr($query, 0, -2); //trimming the last ',' character.
        $query .= ");";

        //echo $query;
        try {
            mysqli_query($this->connection, $query); 
            echo "table $tablename created <br>";
        }
        catch (mysqli_sql_exception $e) {
            echo "Failed creating table $tablename: <br>$e";
        }
    }
    public function insert($tablename, $columns=[], $values=[]) {
        if(count($columns)==0 or count($values)==0) {
            echo "inserting empty line is not allowed. <br>";
        }
        else if(count($columns) != count($values)) {
            echo count($columns) . " " .  count($values) . "<br>";
            echo "number of columns is not equal to the number of values. <br>";
        }

        else {
            $query = "insert into $tablename ( "; //generating the insert query.
            foreach($columns as $col) {
                $query.="$col, ";
            }
            $query = substr($query,0,-2);
            $query .= ") values ( ";
            foreach($values as $val) {
                $query.="$val, ";
            }
            $query = substr($query,0,-2);
            $query .= ");";
            //echo $query;
            try{
                mysqli_query($this->connection, $query);
            } 
            catch (mysqli_sql_exception $e) {
                echo "Failed inserting rows: <br>$e ";
            }
        }
    }
    public function delete_table($tablename) {
        $query = "drop table $tablename;";
        
        try {
            mysqli_query($this->connection, $query) ;
            echo "table '$tablename' deleted successfully";
        } 
        catch (mysqli_sql_exception $e) {
            echo "Failed to delete '$tablename' table: <br>$e"; 
        }
    }
    public function delete_row($tablename, $condition) {
        $query = "delete from $tablename where $condition;";
        
        try {
            mysqli_query($this->connection, $query) ;
            echo "row deleted successfully";
        } 
        catch (mysqli_sql_exception $e) {
            echo "Failed to delete '$tablename' table: <br>$e"; 
        }
    }
    public function update($tablename, $field, $newdata, $condition) {
        $query = "update $tablename set $field = $newdata where $condition;";
        // echo $query;
        try {
            mysqli_query($this->connection, $query) ;
            //table deleted successfully
        }  
        catch (mysqli_sql_exception $e) {
            echo "updating failed: <br> $e";
        }
    }
    
    public function select($tables, $columns = "*", $condition = ""){
        $query="select $columns from $tables $condition;";
        //echo $query."<br><br><br><br>";
        try {
            $result = mysqli_query($this->connection, $query);
        }
        catch (mysqli_sql_exception $e) {
            echo "updating failed: <br> $e";
        }
        // while($row = mysqli_fetch_assoc($result)){
        //     // echo "<pre>";
        //     // print_r($row);
        //     // echo "</pre>";
        //     foreach($row as $val){
        //         echo "$val ";
        //     }
        //     echo "<br>";
        // }
        return $result;
    }
    
    private function connect(){
        $this->connection = mysqli_connect($this->server, $this->username, $this->password, $this->dbname);
        if(!($this->connection)){
            die("connecting to database failed. <br>");
        }
    }
    public function close_connection() {
        if ($this->connection) {
            mysqli_close($this->connection);
        }
    }
}
function download_photo($img_url = IMG_URL, $images_folder = IMG_FOLDER_PATH){
    if (!file_put_contents($images_folder."profile.jpg", file_get_contents($img_url))) {
        echo "failed to save the image.";
    }
}
function post_count($users_table_name = USERS_TABLE_NAME, $posts_table_name = POSTS_TABLE_NAME, 
                    $servername = SERVERNAME, $username = USERNAME,$password = PASSWORD,$dbname = DBNAME) 
{
    $db_obj = new Database($servername, $username, $password, $dbname);
    $tables = $posts_table_name;
    $columns = "DATE(posted_date) AS post_date, SUBSTRING(posted_time, 1, 2) AS post_hour, COUNT(*) AS post_count";
    $condition = "GROUP BY post_date, post_hour ORDER BY post_date, post_hour";
    $result = $db_obj->select($tables, $columns, $condition);
    $db_obj->close_connection();
    return $result;
}
function birthday_post($month ,$users_table_name = USERS_TABLE_NAME, $posts_table_name = POSTS_TABLE_NAME, 
                        $servername = SERVERNAME, $username = USERNAME,$password = PASSWORD,$dbname = DBNAME)
{
    $db_obj = new Database($servername, $username, $password, $dbname);
    $tables = "posts p";
    $columns = "p.*, subquery.user_id";
    $condition = "join ( select u.user_id, max(concat(p.posted_date, ' ', p.posted_time)) as max_datetime from users u join posts p on u.user_id = p.author_id where u.birthday LIKE '_____".$month."___' group by u.user_id ) subquery on p.author_id = subquery.user_id and concat(p.posted_date, ' ', p.posted_time) = subquery.max_datetime";
    $result = $db_obj->select($tables, $columns, $condition);
    $db_obj->close_connection();
    return $result;
}
function extract_feed_data($users_table_name = USERS_TABLE_NAME, $posts_table_name = POSTS_TABLE_NAME, 
                        $servername = SERVERNAME, $username = USERNAME,$password = PASSWORD,$dbname = DBNAME) 
{ 
    $tables = "$users_table_name u, $posts_table_name p";
    $condition = "where u.user_id = p.author_id and u.active_user = true";
    $db_obj = new Database($servername, $username, $password, $dbname);
    $result=$db_obj->select($tables, "*", $condition);
    $db_obj->close_connection();
    return $result;
}  
function api_call($apiurl = ""){

    
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, $apiurl);//setting API url
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//setting options to return the response as a string rather than ouput it
    
    $response = curl_exec($curl);//sending the url through the browser
    
    if(curl_errno($curl)){
        echo curl_error($curl);
    } 
    else {
        curl_close($curl);
        return $response;
    }
}
function rand_date(){
    $strt = strtotime("100 years ago");
    $end = strtotime("today");
    return date("Y-m-d",mt_rand($strt, $end));  
}
function rand_time(){
    $strt = strtotime("today");
    $end = strtotime("tomorrow");
    return date("H:m:s",mt_rand($strt, $end));  
}
function init_database(
    $users_api_url = USERS_API_URL,
    $posts_api_url = POSTS_API_URL,
    $dbname = DBNAME,
    $servername = SERVERNAME,
    $username = USERNAME,
    $password = PASSWORD,
    $users_table_name = USERS_TABLE_NAME,
    $users_columns = USERS_COLUMNS,
    $posts_table_name = POSTS_TABLE_NAME,
    $posts_columns = POSTS_COLUMNS,
    $users_col_names = USERS_COL_NAMES,
    $posts_col_names = POSTS_COL_NAMES
) {
   
    //echo "INIT_DATABASE";
    $res=api_call($users_api_url);
    $parsed_users = json_decode($res);

    $res=api_call($posts_api_url);
    $parsed_posts = json_decode($res);

    //create database 
    Database::create_db($dbname,$servername,$username,$password);
    //connect to database
    $db_obj = new Database($servername, $username, $password, $dbname);
    //create tables
    $db_obj->create_table($users_table_name, $users_columns);
    $db_obj->create_table($posts_table_name, $posts_columns);
    //insert rows
    
    foreach ($parsed_users as $index => $row) {
        $db_obj->insert(
            $users_table_name, 
            $users_col_names, 
            [$row->id, "'".$row->email."'", "'".rand_date()."'" ,mt_rand(0, 1)]
        );
        //echo "$row->id $row->email" . rand(0, 1);
        if ($index > 20) {
            break;
        }
    }
    
    foreach ($parsed_posts as $index => $row) {
        $db_obj->insert(
            $posts_table_name,
            $posts_col_names,
            [$row->id, $row->userId, "'".$row->title."'", "'".$row->body."'", "'".rand_date()."'", "'".rand_time()."'",rand(0, 1)]
        );
        if ($index > 50) {
            break;
        }
    }
    
    $db_obj->close_connection();
}
?>