<?php include('script/header.php'); ?>

<div class = "container">

<?php
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

<div class = "row col-md-12">
    <div class = "col-md-3"></div>
    <div class = "col-md-6">
        <p> Album: </p>
        <table class="log">
        <tr class="log">
            <td class='button'><a class = 'button' href='album.php'>ALL</a></td>
            <td class='button'><a class = 'button' href='album.php?countryid=-1'>No winning</a></td>
                <?php
                    $sql = 'SELECT country,countryid FROM GrandPrix;';
                    $result = $mysqli->query($sql);
                    if (!$result) {
                        print($mysqli->error);
                        exit();
                    }
                    $i = 2;
                    while ($row = $result->fetch_assoc()) {
                        $link = $row['countryid'];
                        $href = "album.php?countryid=$link";
                        if($i%4==3){
                            echo "<td class='button'><a class = 'button' href='$href' title='$href'>{$row['country']}</a></td></tr>";
                        }
                        else if($i%4==0){
                            echo "<tr><td class='button'><a class = 'button' href='$href' title='$href'>{$row['country']}</a></td>";
                        }
                        else{
                            echo "<td class='button'><a class = 'button' href='$href' title='$href'>{$row['country']}</a></td>";
                        }
                        $i += 1;
                    }



                    if ($i%4!=0){
                        $missing = '</tr>';
                        while ($i%4!=0){
                            $missing = '<td> &nbsp; </td>'.$missing;
                            $i += 1;
                        }
                        echo "$missing";
                    }

                ?>


       </table>

    </div>
</div>


<div class = "row col-md-12">
    <div class = "col-md-1"></div>
    <?php

        require_once 'script/config.php';
        //Establish a database connection
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        //Was there an error connecting to the database?
        if ($mysqli->errno) {
            //The page isn't worth much without a db connection so display the error and quit
            print($mysqli->error);
            exit();
        }


        $country_select = filter_input( INPUT_GET, 'countryid', FILTER_SANITIZE_STRING );

        if(!empty($country_select) && $country_select>0){
            $sql = "SELECT name, url, year, country, birth, credit,Driver.driverid as id FROM Winner inner join Driver on Driver.driverid=Winner.driverid inner join GrandPrix on Winner.countryid = GrandPrix.countryid WHERE Winner.countryid = '$country_select' ORDER BY year desc;";
        }
        elseif($country_select==-1) {
            $sql = " SELECT name, url, birth, credit, Driver.driverid as id FROM Driver left join Winner on Driver.driverid=Winner.driverid Where countryid is null GROUP BY name ORDER BY name asc;";
        }
        else{
            $sql = " SELECT name, url, birth, credit, Driver.driverid as id FROM Driver GROUP BY name ORDER BY name asc;";
        }

        $result = $mysqli->query($sql);

        //If no result, print the error
        $country = "";

        if(!empty($country_select) && $result && $country_select>0){
            $row = $result->fetch_assoc();
            $country = $row['country'];
        }
        elseif($country_select==-1){
            $country = "No Winning";
        }

        print("<table class='photo'><tr><td colspan='2' class='title'>Show $country Winners</td></tr>");
        $result = $mysqli->query($sql);
        $i = 0;
            //Loop through the $result rows fetching each one as an associative array
        if(!empty($country_select) && $country_select!=-1){
            while ($row = $result->fetch_assoc()) {
                $link = $row['id'];
                $href = "detail.php?driverid=$link";
                if($i%2==0){
                    echo "<tr>
                        <td class='photo'>
                        <a class='pic' href='$href' title='$href'><img src=".$row[ 'url' ]." alt='0' class = 'pic'></a>
                        <p>{$row['name']} ({$row['year']})</p>
                        <p class='cite'>{$row['credit']}</p></td>";
                }
                else{
                    echo "
                        <td class='photo'>
                        <a class='pic' href='$href' title='$href'><img src=".$row[ 'url' ]." alt='0' class = 'pic'></a>
                        <p>{$row['name']} ({$row['year']})</p>
                        <p class='cite'>{$row['credit']}</p></td></tr>";
                }
                $i += 1;
            }
        }
        else{
            while ($row = $result->fetch_assoc()) {
                $link = $row['id'];
                $href = "detail.php?driverid=$link";
                if($i%2==0){
                    echo "<tr>
                        <td class='photo'>
                        <a class='pic' href='$href' title='$href'><img src=".$row[ 'url' ]." alt='0' class = 'pic'></a>
                        <p>{$row['name']} ({$row['birth']})</p>
                        <p class='cite'>{$row['credit']}</p></td>";
                }
                else{
                    echo "
                        <td class='photo'>
                        <a class='pic' href='$href' title='$href'><img src=".$row[ 'url' ]." alt='0' class = 'pic'></a>
                        <p>{$row['name']} ({$row['birth']})</p>
                        <p class='cite'>{$row['credit']}</p></td></tr>";
                }
                $i += 1;
            }
        }

        if($i%2!=0)
            print("<td></td></tr>");
        if(isset($_SESSION['logged_user_by_sql']) && !empty($country_select)){
            $href = "edit_album.php?countryid=$country_select";
            print("<tr><td colspan='2' class='button'><a class = 'button' href='$href'>Edit Album</a></td></tr>");
        }
        print("</table>");

?>

</div>

<div class="col-md-12 footer">Copyright BY Yue Shi</div>
</div>
</body>
</html>