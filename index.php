<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once("./config.php"); ?>
    <?php include_once("./utils.php"); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/style.css">
    <title>Feed</title>

</head>
<body>
    <div class = "container">
    <nav style="text-align:center;">
    <form action=<?php echo "'".$_SERVER["PHP_SELF"]."'";?> method=get >
        <input type="submit" name="Home" value="Home">
   
        <input type="submit" name="birthday" value="birthday">
        
        <input type="submit" name="Post count" value="Post count">
        <div>
            <select name="month">
                <?php foreach(MONTHS_NAMES as $key=>$month){ ?>
                    <option value=<?php echo "'$key'" ;?>><?php echo $month ;?></option>
               <?php } ?>
            </select>
        </div>
    </form>
    </nav>
        
        <pre><?php //print_r($_GET)?></pre>
        
    
    <?php
        if(isset($_GET['birthday'])){
            //echo "BIRTHDAY<br>";
            $result=birthday_post($_GET['month']);
            $is_feed = true;
        } else if(isset($_GET['Post_count'])){
            //echo "POST COUNT<br>";
            
            $result=post_count();
            $is_feed = false;
        } else {
            //echo "FEED<br>";
            
            $result=extract_feed_data();
            $is_feed = true;
        }
        ?>
       
       <?php
    if($is_feed){
        echo "<h1>FEED</h1>";
        //print_r($result);
        if($result->num_rows == 0 && isset($_GET['month'])){
            echo "No user has birthday in ". MONTHS_NAMES[$_GET['month']];
        } else {  
            while($row = mysqli_fetch_assoc($result)){
                echo "<div class='post'>";
                echo "<img class='user-photo' src=".IMG_FOLDER_PATH."profile.jpg alt='User Photo'>";
                echo "<div class='post-title'>Post by User #" . $row['user_id'] . "</div>";
                echo "<div class='post-title'>" . $row['title'] . "</div>";
                echo "<div class='post-content'>" . $row['content'] . "</div>";
                echo "<div class='posted-date'>Posted on: " . $row['posted_date'] . "</div>";
                echo "</div>";           
            }
        }
    } else {  
        echo "<h1>POSTS PER HOUR</h1>";
        ?>
        <table class='table table-hover table-brodered table-striped'>
            <thead>
                <tr>
                    <th>post_date</th>
                    <th>post_hour</th>
                    <th>post_count</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)){ ?>
                    <tr>
                        <?php foreach($row as $key=>$val) { ?>
                            <td><?php echo $val; if($key == 'post_hour') {echo ":00";}; ?></td>
                            <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
            
            
        </table>
        <?php } ?>
        
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    </body>
</html>
<?php

?>