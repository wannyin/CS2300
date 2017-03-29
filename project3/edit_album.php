<?php include('script/header.php'); ?>
<div class="container">
<div class="row content"></div>

<?php
if(isset($_SESSION['logged_user_by_sql'])){

    require_once 'script/config.php';
        //Establish a database connection
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        //Was there an error connecting to the database?
        if ($mysqli->errno) {
            //The page isn't worth much without a db connection so display the error and quit
            print($mysqli->error);
            exit();
        }

    $countryid = filter_input( INPUT_GET, 'countryid', FILTER_SANITIZE_NUMBER_INT );
?>

<?php
    $msg ="";

    if(isset($_POST['delete'])){
        $sql_delete_album = "DELETE from GrandPrix where countryid = $countryid;";
        $sql_delete_winner = "DELETE from Winner where countryid = $countryid;";

        if($mysqli->query($sql_delete_album) && $mysqli->query($sql_delete_winner))
            $msg .= "Delete successfully.";
        else
            $msg .= "Delete error.";
    }
?>

<?php
    //http://stackoverflow.com/questions/17577584/php-check-user-input-date
    function checkCityOrCountry($name)
    {
        $countries = file_get_contents('https://api.vk.com/method/database.getCountries?need_all=1&count=1000&lang=en');
        $arr = json_decode($countries, true);
        foreach ($arr['response'] as $country) {
            if (mb_strtolower($country['title']) === mb_strtolower($name)) {
                return true;
            }
        }
        return false;
    }

    if(isset($_POST['edit'])){

        $error = false;

        $country = filter_input( INPUT_POST, 'edit_country', FILTER_SANITIZE_STRING );
        $city = filter_input( INPUT_POST, 'edit_city', FILTER_SANITIZE_STRING );

        $country = ucfirst($country);
        $city = ucfirst($city);

        if (!empty($country) && !empty($city)) {
            if(!checkCityOrCountry($country)) {
                $msg.="Invalid COUNTRY value. ";
                $error=true;
            }

            if (!$error){
                $sql = "UPDATE GrandPrix SET country = '$country', city = '$city' where countryid = '$countryid';";
                //$html_safe_sql = htmlentities( $sql );

                if( $mysqli->query($sql)) {
                    $msg.= 'Grand Prix Edited.';
                }
                else{
                    $msg.= 'Editing fail.';
                }
            }
        }
        else{
            $msg .='Incomplete Grand Prix Information.';
        }
    }
?>

<?php
    if(!empty($countryid)){
        $sql = "SELECT country, city FROM GrandPrix WHERE countryid = '$countryid';";
        $result = $mysqli->query($sql);

        if (!$result) {
            print($mysqli->error);
            exit();
        }
        //$html_safe_sql = htmlentities( $sql );

        if($row = $result->fetch_assoc()){
            $old_country = $row['country'];
            $old_city = $row['city'];
?>

            <div class = "row col-md-12">
                <div class = "col-md-3"></div>
                <div class = "col-md-6">
                <form method="post" enctype="multipart/form-data">
                    <table class="log">
                    <tr class="log">
                        <td colspan='2' class="title">Edit <?php echo $old_country;?> Grand Prix</td>
                    </tr>
                    <tr class="log">
                        <td class="log">Country: </td>
                        <td><input type="text" name="edit_country" value="<?php echo $old_country;?>"/></td>
                    </tr>
                    <tr class="log">
                        <td class="log">City: </td>
                        <td><input type="text" name="edit_city" value= "<?php echo $old_city;?>"/></td>
                    </tr>
                    <tr class="log">
                        <td class="log"></td>
                        <td><input type="submit" name="delete" value="DELETE" class="search" onclick="return confirm('Are you sure to delete the Grand Prix?')"/><input type="submit" name="edit" value="EDIT" class="search"/></td>
                    </tr>
                   </table>
                </form>
                </div>
            </div>

            <div class = 'row'>
                <div class='col-md-3'></div>
                <div class='col-md-6 respond'><p><?php echo $msg;?></p></div>
            </div>
<?php
        }
        else{
?>
            <div class = 'row'><div class='col-md-12 respond'><p><?php echo $msg;?></p><p>Invaild id. Click here to go back to the <a class = 'button' href='album.php'>album</a> page.</p></div></div>

<?php
        }
    }
    else{
?>
        <div class = 'row'><div class='col-md-12 respond'><p><?php echo $msg;?></p><p>No id. Click here to go back to the <a class = 'button' href='album.php'>album</a> page.</p></div></div>

<?php
    }
}
else{
?>
    <div class = 'row'><div class='col-md-12 respond'><p>Please <a class = 'button' href='index.php'>log in</a> before editing driver/grand prix information. Click here to go back to the <a class = 'button' href='album.php'>album</a> page.</p></div></div>

<?php
}
?>

<div class="col-md-12 footer">Copyright BY Yue Shi</div>
</div>
</body>
</html>