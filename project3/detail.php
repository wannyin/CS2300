<?php include('script/header.php'); ?>

<div class = "container">
<div class = "row content"></div>

<?php
    require_once 'script/config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($mysqli->errno) {
        print($mysqli->error);
        exit();
    }

    echo "<div class = 'row col-md-12'><div class = 'col-md-1'></div>";

    $driverid = filter_input( INPUT_GET, 'driverid', FILTER_SANITIZE_NUMBER_INT );

    if(!empty($driverid)){
        $sql = "SELECT name, url, year, country, city, birth, credit FROM Driver left join Winner on Driver.driverid=Winner.driverid left join GrandPrix on Winner.countryid = GrandPrix.countryid WHERE Driver.driverid = '$driverid' ORDER BY year desc;";
        $result = $mysqli->query($sql);

        if (!$result) {
            print($mysqli->error);
            exit();
        }
        //$html_safe_sql = htmlentities( $sql );

        if($row = $result->fetch_assoc()){

            echo "<table class = 'photo'><tr><td class='title'>Driver Detail Page</td></tr>";
            echo "<tr><td>
                    <a class='pic' href='album.php' title='album.php'><img src=".$row[ 'url' ]." alt='0' class = 'full'></a>
                    <p class='cite'>{$row['credit']}</p></td></tr>";
            echo "<tr><td><p>Name: {$row['name']}</p>
                        <p>Birth: {$row['birth']}</p>";
            if ($row['year'])
                echo "<p>Season: </p><p>{$row['year']} {$row['country']}({$row['city']})</p>";

            while ($row = $result->fetch_assoc())
                echo "<p>{$row['year']} {$row['country']}({$row['city']})</p>";

            $href = "edit_driver.php?driverid=$driverid";

            if(isset($_SESSION['logged_user_by_sql']))
                echo "</td></tr><tr><td class='button'><a class = 'button' href='$href'>Edit</a>";
            echo "</td></tr></table>";
        }
        else{
            echo "<div class = 'row'><div class='col-md-11 respond'><p>Invaild id. Click here to go back to <a class = 'button' href='album.php'>album</a> page.</p></div></div>";
        }
    }
    else{
        echo "<div class = 'row'><div class='col-md-11 respond'><p>No id. Click here to go back to <a class = 'button' href='album.php'>album</a> page.</p></div></div>";
    }
    echo "</div>";
?>

<div class="col-md-12 footer">Copyright BY Yue Shi</div>
</div>
</body>
</html>