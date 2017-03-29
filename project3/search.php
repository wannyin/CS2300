<?php include('script/header.php'); ?>
<div class="container">
<div class="row content">
</div>

<?php
    require_once 'script/config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($mysqli->errno) {
        print($mysqli->error);
        exit();
    }
?>

<div class = "row col-md-12">
    <div class = "col-md-3"></div>
    <div class = "col-md-6">
    <form method="post">
        <table class="log">
        <tr class="log">
            <td class="item">Search by </td>
            <td class="log">Driver: <input type="text" name="driver_name"/></td>
            <td class="log">Season: <input type="text" name="season"/></td>
            <td class="log">Grand Prix<input type="text" name="country"/></td>
        </tr>
        <tr>
            <td colspan='3' ></td>
            <td><input type="submit" name="search" value="SEARCH" class="log_in"/></td>
        </tr>
       </table>
    </form>
    </div>
</div>

<div class = "row col-md-12">
    <div class = "col-md-1"></div>

<?php
function getwinning($id){
    require_once 'script/config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT country,year FROM Winner inner join GrandPrix on Winner.countryid = GrandPrix.countryid WHERE driverid =$id;";
    //$html_safe_sql = htmlentities( $sql );
    //print ($html_safe_sql);

    $result = $mysqli->query($sql);

    $winner = "";
    if ($result) {
        while ($row = $result->fetch_assoc()){
            $winner.= "<p>Season: {$row['year']}  {$row['country']}</p>";
        }
    }
    return $winner;
}

if (isset($_POST['search'])){

    $error = false;
    $msg = "Error: ";

    $driver_select = filter_input( INPUT_POST, 'driver_name', FILTER_SANITIZE_STRING );
    $season_select = filter_input( INPUT_POST, 'season', FILTER_SANITIZE_NUMBER_INT );
    $country_select = filter_input( INPUT_POST, 'country', FILTER_SANITIZE_STRING );

    $driver_select = $mysqli->real_escape_string($driver_select);
    $season_select = $mysqli->real_escape_string($season_select);
    $country_select = $mysqli->real_escape_string($country_select);
    $country_select = strtoupper($country_select);

    if ( !empty($season_select) || ! empty($driver_select) || ! empty($country_select)){
        if(!empty($driver_select) && (!preg_match('/^[a-zA-Z]+[a-zA-Z ]*$/',$driver_select) || strlen($driver_select)>50)) {
            $msg.="Invalid DRIVER value. ";
            $error=true;}

        if( !empty($season_select) && ($season_select < 1960 || $season_select > 2017)) {
                $msg.="Invalid SEASON value. ";
                $error=true;}

        if(!empty($country_select) && (!preg_match('/^[a-zA-Z]+[a-zA-Z ]*$/',$country_select) || strlen($country_select)>50)) {
            $msg.="Invalid COUNTRY value. ";
            $error=true;}

        if(!$error){
            if(!empty($driver_select)){
                $sql = "SELECT name, url, birth, credit, Driver.driverid as id FROM Winner inner join Driver on Driver.driverid=Winner.driverid inner join GrandPrix on Winner.countryid = GrandPrix.countryid WHERE Driver.name like '%$driver_select%'";
                if(! empty($country_select)){
                    $sql .= "and UPPER((GrandPrix.country) like '%$country_select%'";
                }
                if(! empty($season_select)){
                    $sql .= "and Winner.year = '$season_select'";
                }
                $sql .="GROUP BY name ORDER BY name;";
            }

            elseif(!empty($season_select)){
                $sql = "SELECT name, url, birth, credit, Driver.driverid as id FROM Winner inner join Driver on Driver.driverid=Winner.driverid inner join GrandPrix on Winner.countryid = GrandPrix.countryid WHERE Winner.year = '$season_select'";
                if(! empty($country_select)){
                    $sql .= "and UPPER(GrandPrix.country) like '%$country_select%'";
                }
                $sql .="GROUP BY name ORDER BY name;";
            }

            else{
                $sql = "SELECT name, url, birth, credit, Driver.driverid as id FROM Winner inner join Driver on Driver.driverid=Winner.driverid inner join GrandPrix on Winner.countryid = GrandPrix.countryid WHERE UPPER(GrandPrix.country) like '%$country_select%' GROUP BY name ORDER BY name;";
            }

            $result = $mysqli->query($sql);

            if ($result) {

                print("<table class='photo'><tr><td colspan='2' class='title'>Show Result</td></tr>");
                while ($row = $result->fetch_assoc()){
                    $winner = getwinning($row['id']);
                    $link = $row['id'];
                    $href = "detail.php?driverid=$link";
                    echo "<tr>
                        <td class='photo'>
                        <a class='pic' href='$href' title='$href'><img src=".$row[ 'url' ]." alt='0' class = 'pic'></a>
                        <p class='cite'>{$row['credit']}</p></td>
                        <td><p>Name: {$row['name']}</p>
                            <p>Birth: {$row['birth']}</p>
                            $winner
                        </td></tr>";
                }
                print("</table>");
            }
        }
        else{
            echo "<div class = 'row'><div class='col-md-11 respond'><p>$msg</p></div></div>";
        }
    }
    else{
            echo "<div class = 'row'><div class='col-md-11 respond'><p>Please give valid searching input.</p></div></div>";
    }
}
?>
</div>

<div class="col-md-12 footer">Copyright BY Yue Shi</div>
</div>
</body>
</html>