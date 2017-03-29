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
?>

<?php
    $msg = "";
    $error = false;

    //funtion used for checking whether a country input vaild
    //reference: http://stackoverflow.com/questions/34274393/determine-if-city-in-input-is-valid
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

    if(isset($_POST['new_race'])){
        $new_country = filter_input( INPUT_POST, 'new_country_name', FILTER_SANITIZE_STRING );
        $new_city = filter_input( INPUT_POST, 'new_city_name', FILTER_SANITIZE_STRING );

        if (!empty($new_city) && !empty($new_country)){
            if(!checkCityOrCountry($new_country)) {
                $msg.="Invalid COUNTRY value. ";
                $error=true;
                }

            if(!$error){

                $sql = 'SELECT max(countryid) FROM GrandPrix;';

                $result = $mysqli->query($sql);
                $row = $result->fetch_row();
                $new_country_id = $row[0]+1;
                $new_country = ucfirst($new_country);
                $new_city = ucfirst($new_city);

                $sql = "INSERT INTO GrandPrix ( country, city, countryid ) VALUES ( '$new_country','$new_city','$new_country_id' );";}

            if( ! empty( $sql ) ) {
                if( $mysqli->query($sql) ) {
                    $msg .= 'Grand Prix Saved.';
                }
                else
                    $msg.= 'Grand Prix Existed.';
            }
            else {
                $msg.= "Error saving Grand Prix.";
            }

        }
        else
            $msg .='Incomplete Grand Prix Information.';

        echo "<div class = 'row'><div class='col-md-3'></div>
            <div class='col-md-6 respond'>
            <p>$msg</p>
            </div></div>";
    }



?>

<?php
//http://stackoverflow.com/questions/17577584/php-check-user-input-date
function isRealDate($date)
{
    if (false === strtotime($date))
    {
        return false;
    }
    else
    {
        list($year, $month, $day) = explode('-', $date);
        if (false === checkdate($month, $day, $year) || $year>2000 || $year<1920)
        {
            return false;
        }
    }
    return true;
}

if(isset($_POST['new_driver'])){
    $msg ="";
    $error = false;

    $driver = filter_input( INPUT_POST, 'new_driver_name', FILTER_SANITIZE_STRING );
    $birth = filter_input( INPUT_POST, 'new_birth', FILTER_SANITIZE_STRING );
    $credit = filter_input( INPUT_POST, 'new_driver_credit', FILTER_SANITIZE_STRING );

    //Check to see if a file was uploaded using the "single file" form
    if ( !empty( $_FILES['new_driver_url'] ) &&  !empty($driver) && !empty($birth)) {

        if(!preg_match('/^[a-zA-Z]+[a-zA-Z ]*[a-zA-Z]+$/',$driver) || strlen($driver)>50) {
            $msg.="Invalid DRIVER name. ";
            $error=true;
        }

        if(!preg_match('/^(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/',$birth) || !isRealDate($birth)){
            $msg.="Invalid BIRTH format. ";
            $error=true;
        }

        if (!$error){
            $newPhoto = $_FILES['new_driver_url'];
            $originalName = $newPhoto['name'];
            if ( $newPhoto['error'] == 0 ) {
                $tempName = $newPhoto['tmp_name'];
                $imageInfo = filesize($tempName);
                if ($imageInfo < 1024000)
                    move_uploaded_file( $tempName, "img/$originalName");
                else{
                    $msg .="The image was too large.";
                    $error = true;
                }

            } else {
                $msg .="The image was not uploaded.";
                $error = true;
            }
        }

        if (!$error){
            $sql = 'SELECT max(driverid) FROM Driver;';

            $result = $mysqli->query($sql);
            $row = $result->fetch_row();
            $driver_id = $row[0]+1;
            $driver = ucfirst($driver);

            $sql = "INSERT INTO Driver ( name, url, driverid, birth, credit ) VALUES ( '$driver','img/$originalName','$driver_id' ,'$birth','$credit');";
        }

        if( ! empty( $sql ) ) {
            if( $mysqli->query($sql) ) {
                $msg.= 'Driver Saved.';
            }
            else{
                $msg.= 'Driver Existed.';
            }
        }

    }
    else{
        $msg .='Incomplete Driver Information.';
    }
    echo "<div class = 'row'><div class='col-md-3'></div>
        <div class='col-md-6 respond'>
        <p>$msg</p>
        </div></div>";
    }
?>

<?php
if(isset($_POST['new_winner'])){
    $winning = filter_input( INPUT_POST, 'new_winning', FILTER_SANITIZE_STRING );
    $error = false;

    if($winning){
        $races = explode(";", $winning);
        $year = array();
        $driver = array();

        foreach ($races as $race) {
            $race = explode(",", $race);
            if(sizeof($race)==2){
                $race[1] = preg_replace("/[^a-zA-Z ]/i", "", $race[1]);

                if( $race[0] <=2017 && $race[0] >= 1960){
                    $sql_race = "SELECT driverid FROM Driver WHERE name = '$race[1]';";
                    $result = $mysqli->query($sql_race);
                    if ($result && $result->num_rows == 1) {
                        $row = $result->fetch_assoc();
                        array_push($year, $race[0]);
                        array_push($driver, $row['driverid']);
                    }
                    else{
                        $error = true;
                        $msg.="Input driver is not exited. ";
                        break;
                    }
                }
                else{
                    $error = true;
                    $msg.="Invalid Winning Season. ";
                    break;
                }
            }
            else{
                $error = true;
                $msg.="Invalid Winning input. ";
                break;
            }
        }

        if (!$error){
            $race = $_POST['new_winner_country'];
            $sql = "SELECT countryid FROM GrandPrix Where country = '$race';";
            $result = $mysqli->query($sql);
            $row = $result->fetch_row();
            $countryid = $row[0];

            if($year){
                $sql_winner = "INSERT INTO Winner ( driverid, countryid, year ) VALUES ";
                for( $i = 0; $i<sizeof($year); $i++ ){
                    $sql_winner .= "('$driver[$i]','$countryid','$year[$i]'),";
                }
                $sql_winner = rtrim($sql_winner,",");
                $sql_winner .=";";
                //$html_safe_sql = htmlentities( $sql_winner );
                //echo $html_safe_sql;
            }

            if( $mysqli->query($sql_winner)) {
                $msg.= 'Winner Add.';
            }
            else{
                $msg.= 'Adding fail.';
            }
        }
    }
    else{
        $msg .='Incomplete Winner Information.';
    }
    echo "<div class = 'row'><div class='col-md-3'></div>
        <div class='col-md-6 respond'>
        <p>$msg</p>
        </div></div>";
}
?>

<?php
/*if(isset($_POST['new_winner'])){
    $msg ="";
    $error = false;

    $driver = $_POST['new_winner_name'];
    $race = $_POST['new_winner_country'];
    $year = filter_input( INPUT_POST, 'new_winner_year', FILTER_SANITIZE_NUMBER_INT );

    if ( !empty($driver) && !empty($race) && !empty($year)){
        if( $year < 1960 || $year > 2017) {
            $msg.="Invalid YEAR value. ";
            $error=true;}

        if (!$error){
            $sql = "SELECT driverid FROM Driver Where name = '$driver';";
            $result = $mysqli->query($sql);
            $row = $result->fetch_row();
            $driver_id = $row[0];

            $sql = "SELECT countryid FROM GrandPrix Where country = '$race';";
            $result = $mysqli->query($sql);
            $row = $result->fetch_row();
            $country_id = $row[0];

            $sql = "INSERT INTO Winner ( driverid, countryid, year ) VALUES ( '$driver_id','$country_id' ,'$year');";
            $check = "SELECT countryid, year From Winner where countryid = '$country_id' and year = '$year';";

            if( ! empty( $check ) && ! empty( $sql )) {
                $check_result = $mysqli->query($check);
                $check_row = $check_result->fetch_row();
                if( $check_row ) {
                    $msg.= 'Winner Existed.';
                }
                elseif( $mysqli->query($sql) ) {
                    $msg.= 'Winner Saved.';
                }
                else{
                    $msg.= 'Winner Saved Error.';
                }
            }
        }
    }
    else{
        $msg.= 'Incomplete Winner Information.';
    }
        echo "<div class = 'row'><div class='col-md-3'></div>
        <div class='col-md-6 respond'>
        <p>$msg</p>
        </div></div>";
}*/
?>

<div class = "row col-md-12">
    <div class = "col-md-1-5"></div>
    <div class = "col-md-3">
    <form method="post" enctype="multipart/form-data">
        <table class="log">
        <tr class="log">
            <td colspan='2' class="title">Add Driver: </td>
        </tr>
        <tr class="log">
            <td class="log">Driver: </td>
            <td><input type="text" name="new_driver_name"/></td>
        </tr>
        <tr class="log">
            <td class="log">Birth: </td>
            <td><input type="text" name="new_birth" value='yyyy-mm-dd'/></td>
        </tr>
        <tr class="log">
            <td class="log">Image:</td>
            <td><input type="file" name="new_driver_url"/></td>
        </tr>
        <tr class="log">
            <td class="log">Credit: </td>
            <td><input type="text" name="new_driver_credit"/></td>
        </tr>
        <tr class="log">
            <td class="log"></td>
            <td><input type="submit" name="new_driver" value="SUBMIT" class="search"/></td>
        </tr>
       </table>
    </form>
    </div>

    <div class = "col-md-3">
    <form method="post">
        <table class="log">
        <tr class="log">
            <td colspan='2' class="title">Add Grand Prix: </td>
        </tr>
        <tr class="log">
            <td class="log">Country: </td>
            <td><input type="text" name="new_country_name"/></td>
        </tr>
        <tr class="log">
            <td class="log">City: </td>
            <td><input type="text" name="new_city_name"/></td>
        </tr>
        <tr class="log"><td> &nbsp; </td><td> &nbsp; </td></tr>
        <tr class="log"><td> &nbsp; </td><td> &nbsp; </td></tr>
        <tr class="log">
            <td></td>
            <td><input type="submit" name="new_race" value="SUBMIT" class="search"/></td>
        </tr>

       </table>
    </form>
    </div>

    <div class = "col-md-3">
    <form method="post">
        <table class="log">
        <tr class="log">
            <td colspan='2' class="title">Add Winner: </td>
        </tr>
        <tr class="log">
            <td class="log">Race: </td>
            <td><select name="new_winner_country" class="button">
                <?php
                    $sql = 'SELECT country FROM GrandPrix;';
                    $result = $mysqli->query($sql);
                    if (!$result) {
                        print($mysqli->error);
                        exit();
                    }
                    $i = 1;
                    while ($row = $result->fetch_assoc()){
                        $country = $row['country'];
                        echo "<option value='$country'>{$row['country']}</option>";
                    }
                ?>
            </select></td>
        </tr>
        <tr class="log">
            <td class="log">Season: </td>
            <td>year,driver;(NO SPACE)</td>
        </tr>
        <tr class="log">
            <td></td>
            <td><textarea rows="5" cols="30" name="new_winning">year,driver;</textarea></td>
        </tr>
<!--       <tr class="log">
            <td class="log">Race: </td>
            <td><select name="new_winner_country" class="button">
                <?php
                    $sql = 'SELECT country FROM GrandPrix;';
                    $result = $mysqli->query($sql);
                    if (!$result) {
                        print($mysqli->error);
                        exit();
                    }
                    $i = 1;
                    while ($row = $result->fetch_assoc()){
                        $country = $row['country'];
                        echo "<option value='$country'>{$row['country']}</option>";
                    }
                ?>
            </select></td>
        </tr>
        <tr class="log">
            <td class="log">Season: </td>
            <td><input type="text" name="new_winner_year"/></td>
        </tr>
        <tr class="log"><td> &nbsp; </td><td> &nbsp; </td></tr>-->
        <tr class="log">
            <td></td>
            <td><input type="submit" name="new_winner" value="SUBMIT" class="search"/></td>
        </tr>
       </table>
    </form>
    </div>
</div>

<?php
}
else{
    echo "<div class = 'row'><div class='col-md-12 respond'><p>Please <a class = 'button' href='index.php'>log in</a> before adding driver/grand prix. Click here to go back to the <a class = 'button' href='album.php'>album</a> page.</p></div></div>";
}
?>



<div class="col-md-12 footer">Copyright BY Yue Shi</div>
</div>
</body>
</html>