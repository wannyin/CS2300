<?php include('script/header.php'); ?>
<div class="container">
<div class="row content"></div>

<?php
if(isset($_SESSION['logged_user_by_sql'])){

    require_once 'script/config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($mysqli->errno) {
        print($mysqli->error);
        exit();
    }

    $driverid = filter_input( INPUT_GET, 'driverid', FILTER_SANITIZE_NUMBER_INT );
    $msg ="";

    if(isset($_POST['delete'])){
        $sql_remove_url = "SELECT url from Driver where driverid = $driverid;";
        $sql_delete_driver = "DELETE from Driver where driverid = $driverid;";
        $sql_delete_winner = "DELETE from Winner where driverid = $driverid;";

        if( $result = $mysqli->query($sql_remove_url)){
            $remove = $result->fetch_row();
            if( $mysqli->query($sql_delete_driver) && $mysqli->query($sql_delete_winner) && unlink($remove[0]))
                $msg .= "Delete successfully.";
        }
        else{
            $msg .= "Delete error.";
        }
    }

    //http://stackoverflow.com/questions/17577584/php-check-user-input-date
    function isRealDate($date)
    {
        if (false === strtotime($date))
            return false;
        else
        {
            list($year, $month, $day) = explode('-', $date);
            if (false === checkdate($month, $day, $year) || $year>2000 || $year<1920)
                return false;
        }
        return true;
    }

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
        $year = array();
        $grandprix = array();

        $driver = filter_input( INPUT_POST, 'edit_driver_name', FILTER_SANITIZE_STRING );
        $birth = filter_input( INPUT_POST, 'edit_birth', FILTER_SANITIZE_STRING );
        $credit = filter_input( INPUT_POST, 'edit_driver_credit', FILTER_SANITIZE_STRING );
        $winning = filter_input( INPUT_POST, 'edit_winning', FILTER_SANITIZE_STRING );

        if (!empty($driver) && !empty($birth)) {
            if(!preg_match('/^[a-zA-Z]+[a-zA-Z ]*[a-zA-Z]+$/',$driver) || strlen($driver)>50) {
                $msg.="Invalid DRIVER name. ";
                $error=true;
            }

            if(!preg_match('/^(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/',$birth) || !isRealDate($birth)){
                $msg.="Invalid BIRTH format. ";
                $error=true;
            }

            if($winning){
                $races = explode("\n", $winning);


                foreach ($races as $race) {
                    $race = explode(",", $race);
                    if(sizeof($race)==2){
                        $race[1] = preg_replace("/[^a-zA-Z]/i", "", $race[1]);

                        if( $race[0] <=2017 && $race[0] >= 1960){
                            $sql_race = "SELECT countryid FROM GrandPrix WHERE country = '$race[1]';";
                            $result = $mysqli->query($sql_race);
                            if ($result && $result->num_rows == 1) {
                                $row = $result->fetch_assoc();
                                array_push($year, $race[0]);
                                array_push($grandprix, $row['countryid']);
                            }
                            else{
                                $error = true;
                                $msg.="Selected grand prix is not exited. ";
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
            }

            if (!$error){
                $driver = ucfirst($driver);
                $sql = "UPDATE Driver SET name = '$driver', birth = '$birth', credit='$credit' where driverid = '$driverid';";
                $sql_2 = "DELETE FROM Winner WHERE driverid = '$driverid';";
                //$html_safe_sql = htmlentities( $sql );

                if( $mysqli->query($sql) && $mysqli->query($sql_2)) {
                    if(sizeof($year)>0){

                        $sql_winner = "INSERT INTO Winner ( driverid, countryid, year ) VALUES ";
                        for( $i = 0; $i<sizeof($year); $i++ ){
                            $sql_winner .= "('$driverid','$grandprix[$i]','$year[$i]'),";
                        }
                        $sql_winner = rtrim($sql_winner,",");
                        $sql_winner .=";";
                        //$html_safe_sql = htmlentities( $sql_winner );
                        if($mysqli->query($sql_winner))
                            $msg.= 'Driver Edited.';
                        else
                            $msg.= 'Edting fail.';
                    }
                }
                else{
                    $msg.= 'Edting fail.';
                }
            }
        }
        else{
            $msg .='Incomplete Driver Information.';
        }
    }

    if(!empty($driverid)){
        $sql = "SELECT name, url, year, country, city, birth, credit FROM Driver left join Winner on Driver.driverid=Winner.driverid left join GrandPrix on Winner.countryid = GrandPrix.countryid WHERE Driver.driverid = '$driverid' ORDER BY year desc;";
        $result = $mysqli->query($sql);

        if (!$result) {
            print($mysqli->error);
            exit();
        }
        //$html_safe_sql = htmlentities( $sql );

        if($row = $result->fetch_assoc()){
            $old_name = $row['name'];
            $old_url = $row['url'];
            $old_birth = $row['birth'];
            $old_credit = $row['credit'];
            $old_winning = "";
            if (!empty($row['year']))
                $old_winning .= $row['year'].",".$row['country'];

            while ($row = $result->fetch_assoc()) {
                $old_winning .= "&#13;&#10;".$row['year'].",".$row['country'];
            }
    ?>

        <div class = "row col-md-12">
            <div class = "col-md-3"></div>
            <div class = "col-md-6">
            <form method="post" enctype="multipart/form-data">
                <table class="log">
                <tr class="log">
                    <td colspan='2' class="title">Edit: </td>
                </tr>
                <tr class="log">
                <td colspan='2'>
                    <img src="<?php print("$old_url");?>" alt='0' class = 'full'></td>
                </tr>
                <tr class="log">
                    <td class="log">Driver: </td>
                    <td><input type="text" name="edit_driver_name" value="<?php echo htmlspecialchars($old_name);?>"/></td>
                </tr>
                <tr class="log">
                    <td class="log">Birth: </td>
                    <td><input type="text" name="edit_birth" value= "<?php echo $old_birth;?>"/></td>
                </tr>
                <tr class="log">
                    <td class="log">Credit: </td>
                    <td><input type="text" name="edit_driver_credit" value="<?php echo $old_credit;?>"/></td>
                </tr>
                <tr class="log">
                    <td class="log">Season: </td>
                    <td><textarea rows="4" cols="50" name="edit_winning"><?php print("$old_winning");?></textarea></td>
                </tr>
                <tr class="log">
                    <td class="log"></td>
                    <td><input type="submit" name="delete" value="DELETE" class="search" onclick="return confirm('Are you sure to delete the driver?')"/><input type="submit" name="edit" value="EDIT" class="search"/></td>
                </tr>
               </table>
            </form>
            </div>
        </div>

        <div class = 'row'>
            <div class='col-md-3'></div>
            <div class='col-md-6 respond'><p><?php echo $msg?></p></div>
        </div>

<?php
        }
        else{
?>
            <div class = 'row'><div class='col-md-12 respond'><p><?php echo $msg?></p><p>Invaild id. Click here to go back to the <a class = 'button' href='album.php'>album</a> page.</p></div></div>

<?php
        }
    }
    else{
?>
        <div class = 'row'><div class='col-md-12 respond'><p>No id. Click here to go back to the <a class = 'button' href='album.php'>album</a> page.</p></div></div>

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