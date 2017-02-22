<!DOCTYPE html>
<html>
<head>
    <title>Online Catalog</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet">
</head>
<body>

<!--open data file-->
<?php
    $data_file_name = 'data.txt';
    $file_pointer = fopen($data_file_name, 'r');
    if (!$file_pointer ) {
        print( 'error' );
        exit;
    }
    $championships = array();
    while(!feof($file_pointer)){
        $line = fgets($file_pointer);
        $championship = explode(",", $line);
        $championship[9] = trim($championship[9]);
        $championships[] = $championship;
    };

    //sort data by year
    function cmp($a, $b) {
            return $a[0] - $b[0];
    }

    usort($championships, "cmp");

    unset( $championship );
    unset ( $line );
    fclose( $file_pointer );
    if ( ! is_array( $championships ) ) {
            print("<p>There was an error reading the file $data_file_name</p>");
            exit;
        }
    $update = false;
    $count = count($championships);

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

?>

<!--header-->
<div class="container">
    <div class="row banner">
        <div class="col-md-12" id="title_1">Formula One World Champions<br><div id="title_2">in the History</div></div>
    </div>

<!--show data table-->
<?php
if(isset($_POST["show_all"])){
    echo "<div class='row content' id='result'>
        <div class='col-md-1'></div>
        <div class='col-md-10'>
            <table class='show'>
                <thead class='show'>
                    <th class='show'>Season</th>
                    <th class='show'>Country</th>
                    <th class='show'>Driver</th>
                    <th class='show'>Car No.</th>
                    <th class='show'>Team</th>
                    <th class='show'>Poles</th>
                    <th class='show'>Wins</th>
                    <th class='show'>Podiums</th>
                    <th class='show'>Fastest Laps</th>
                    <th class='show'>Points</th>
                </thead>";

                    foreach ($championships as $championshipindex => $championship) {
                        print("<tr class='show'>");
                        for($j=0;$j<count($championship);++$j) {
                            $safe_element = htmlentities( $championship[$j] );
                            //change format of driver's name
                            if ($j==2)
                                $safe_element = ucfirst($safe_element);
                            //display the country flag if the flag is available, otherwise display the name
                            if ($j==1){
                                if(strtoupper($safe_element)=="GERMANY")
                                $safe_element = "<img src='./image/germany.png' class='flag'/>";
                                else if (strtoupper($safe_element)=="UNITED KINGDOM")
                                $safe_element = "<img src='./image/england.png' class='flag'/>";
                                else if (strtoupper($safe_element)=="FINLAND")
                                $safe_element = "<img src='./image/finland.png' class='flag'/>";
                                else if (strtoupper($safe_element)=="SPAIN")
                                $safe_element = "<img src='./image/spain.png' class='flag'/>";
                                else if (strtoupper($safe_element)=="BRAZIL")
                                $safe_element = "<img src='./image/brazil.png' class='flag'/>";
                                else{$safe_element = strtoupper($safe_element);}
                            }
                            print("<td>$safe_element</td>");
                        }
                        print("</tr>");
                    }


           echo  "</table></div><div class='col-md-1 content'></div></div>";
    };
?>

<!--add funtion-->
<?php
if (isset($_POST["add"])) {
    echo "<div class='row content'>
        <div class='col-md-12'></div></div>";
    echo "<div class='row content'>
        <div class='col-md-1'></div>
        <div class='col-md-10'>";

    $error = false;
    $msg = "Error: ";

    $season = filter_input( INPUT_POST, 'season', FILTER_SANITIZE_NUMBER_INT);
    $driver = filter_input( INPUT_POST, 'driver', FILTER_SANITIZE_STRING );
    $nationality = filter_input( INPUT_POST, 'nationality', FILTER_SANITIZE_STRING );
    $car = filter_input( INPUT_POST, 'car', FILTER_SANITIZE_NUMBER_INT );
    $team = $_POST["team"];
    $poles = filter_input( INPUT_POST, 'poles', FILTER_SANITIZE_NUMBER_INT );
    $wins = filter_input( INPUT_POST, 'wins', FILTER_SANITIZE_NUMBER_INT );
    $podiums = filter_input( INPUT_POST, 'podiums', FILTER_SANITIZE_NUMBER_INT );
    $laps = filter_input( INPUT_POST, 'laps', FILTER_SANITIZE_NUMBER_INT );
    $points = filter_input( INPUT_POST, 'points', FILTER_SANITIZE_NUMBER_INT );

    //check input
    if ( ! empty($season) && ! empty($driver) && ! empty($nationality) && ! empty($car)) {
        //name should only contains character, space and less than 50 characters
        if(!preg_match('/^[a-zA-Z]+[a-zA-Z ]*[a-zA-Z]+$/',$driver) || strlen($driver)>50) {
            $msg.="Invalid DRIVER value. ";
            $error=true;}
        //should be a valid country name
        if(!checkCityOrCountry($nationality)) {
            $msg.="Invalid COUNTRY value. ";
            $error=true;}
        //season limit
        if( $season < 1960 || $season > 2017) {
            $msg.="Invalid SEASON value. ";
            $error=true;}
        //car number limit
        if( $car < 0 || $car > 99 || !preg_match('/^[1-9]+/',$car)) {
            $msg.="Invalid CAR NO. value. ";
            $error=true;}
        //no of stations limit
        if( empty($poles)) $poles = "N/A";
        else if($poles>25 || $poles<0) {
            $msg.="Invalid POLES value. Must be within 0-25. ";
            $error=true;}
        if( empty($wins)) $wins = "N/A";
        else if($wins>25 || $wins<0) {
            $msg.="Invalid WINS value. Must be within 0-25. ";
            $error=true;}
        if( empty($podiums)) $podiums = "N/A";
        else if($podiums>25 || $podiums<0) {
            $msg.="Invalid PODIUMS value. Must be within 0-25. ";
            $error=true;}
        if( empty($laps))  $laps = "N/A";
        else if($laps>25 || $laps<0) {
            $msg.="Invalid FASTEST LAPS value. Must be within 0-25. ";
            $error=true;}
        if( empty($points)) $points = "N/A";
        else if($points>600 || $points<0) {
            $msg.="Invalid POINTS value. Must be within 0-25. ";
            $error=true;}
        //wins must be smaller of equal to podiums
        if(!empty($wins) && !empty($podiums) && $wins>$podiums){
            $msg.="PODIUMS must be greater or equal to WINS";
            $error=true;}

        for($i=0;$i<$count;++$i){
            //duplicate season is not allowed
            if ($season===$championships[$i][0]){
                $msg.="Input season existed.";
                $error = true;
                break;
            }
            //the same driver should from the same country and has the same car no after 2011
            if ($driver===$championships[$i][2]){
                if($nationality != $championships[$i][1] ||($car!=$championships[$i][3] && $championships[$i][0]>2011 && $car!= 1)){
                    $msg.="Input driver info conflicted.";
                    $error = true;
                    break;
                }
            }
            //after 2011, every driver has unique car number, expect that car number 1 can have different owners
            if ($car==$championships[$i][3] && $championships[$i][0]>2011 && $car!= 1){
                if($driver != $championships[$i][2]){
                    $msg.="Input driver info conflicted.";
                    $error = true;
                    break;
                }
            }
        }

        $new_champion = array( $season, $nationality, $driver,$car, $team,$poles,$wins, $podiums, $laps, $points);
        $championships[] = $new_champion;

        if(!$error) $update = true;
    }

    else{
        $msg.="Missing required information.";
        $error=true;
    }

    if ($update) {
        $file_pointer = fopen($data_file_name,'w');

        if (!$file_pointer) {
            print( "<p>Can't open $data_file_name for writing.</p>");
            exit;
        }

        usort($championships, "cmp");

        $lines = array();
        foreach ($championships as $championship) {
            $line = implode( ",", $championship );
            $lines[] = $line;
        }

        $contents = implode( "\n", $lines );
        fputs($file_pointer, $contents );
        $closed = fclose( $file_pointer );
        if( $closed ) {
            print "<p class='respond'>Saved the update</p>";
        }
        else {
            print '<p class="respond">Update failed</p>';
        }
    }

    if($error){
        echo "<p class='respond'>$msg</p>";
    }

    echo "</div><div class='col-md-1'></div></div></div>";
}
?>

<!--search funtion-->
<?php
if(isset($_POST["search"])){
    $input_error = false;
    $input_msg = "Error: ";
    echo "<div class='row content' id='result'>
            <div class='col-md-1'></div>
            <div class='col-md-10'>
                <table class='show'>
                    <thead class='show'>
                        <th class='show'>Season</th>
                        <th class='show'>Country</th>
                        <th class='show'>Driver</th>
                        <th class='show'>Car No.</th>
                        <th class='show'>Team</th>
                        <th class='show'>Poles</th>
                        <th class='show'>Wins</th>
                        <th class='show'>Podiums</th>
                        <th class='show'>Fastest Laps</th>
                        <th class='show'>Points</th>
                    </thead>";

    $season_search = filter_input( INPUT_POST, 'season_search', FILTER_SANITIZE_NUMBER_INT );
    $driver_search = filter_input( INPUT_POST, 'driver_search', FILTER_SANITIZE_STRING );
    $nationality_search = filter_input( INPUT_POST, 'nationality_search', FILTER_SANITIZE_STRING );
    $team_search = $_POST["team_search"];
    $answer = array();

    //check input
    if(empty($season_search) && empty($driver_search) && empty($nationality_search) && $team_search==="null"){
        $input_msg.="No valid input. ";
        $input_error = true;
    }

    else {
        if(!empty($season_search) && ($season_search>2017 || $season_search<1960)){
            $input_msg.="Invalid SEASON value. ";
            $input_error = true;
        }
        if(!empty($nationality_search) && !checkCityOrCountry($nationality_search)){
            $input_msg.="Invalid COUNTRY value. ";
            $input_error = true;
        }
        if(!empty($driver_search) && !preg_match('/^[a-zA-Z]+[a-zA-Z ]*[a-zA-Z]+$/',$driver_search) || strlen($driver_search)>50){
            $input_msg.="Invalid DRIVER value. ";

            $input_error = true;
        }
    }

    if($input_error === true){
            echo "<tr class='show'><td colspan='10' class='search'>$input_msg</td></tr>";
            echo  "</table></div><div class='col-md-1 content'></div></div>";
    }

    if ( !empty($season_search) && $input_error != true)
        for($i=0;$i<$count;++$i){
            if($championships[$i][0]==$season_search)
                $answer[] = $championships[$i];
        }


    if ( $team_search != "null" && $input_error != true)
        for($i=0;$i<$count;++$i){
            if($championships[$i][4]==$team_search)
                $answer[] = $championships[$i];
        }

    if ( !empty($nationality_search) && $input_error != true)
        for($i=0;$i<$count;++$i){
            if(strtoupper($championships[$i][1])==strtoupper($nationality_search))
                $answer[] = $championships[$i];
        }

    if ( !empty($driver_search) && $input_error != true)
        for($i=0;$i<$count;++$i){
            $name = explode(" ",$championships[$i][2]);
            $name = array_map('strtoupper', $name);
            if(in_array(strtoupper($driver_search),$name) ||strtoupper($driver_search)===strtoupper($championships[$i][2]))
                $answer[] = $championships[$i];
        }

    usort($answer, "cmp");


    //show search results
    if(!empty($answer)){
        for($i=0;$i<count($answer);++$i) {
            $championship = $answer[$i];
            if ($i===0 || $i>=1 && $answer[$i][0]!=$answer[$i-1][0]){
                print("<tr class='show'>");
                for($j=0;$j<count($championship);++$j) {
                    $safe_element = htmlentities( $championship[$j] );
                    if ($j==2)
                        $safe_element = ucfirst($safe_element);
                    if ($j==1){
                        if(strtoupper($safe_element)=="GERMANY")
                        $safe_element = "<img src='./image/germany.png' class='flag'/>";
                        else if (strtoupper($safe_element)=="UNITED KINGDOM")
                        $safe_element = "<img src='./image/england.png' class='flag'/>";
                        else if (strtoupper($safe_element)=="FINLAND")
                        $safe_element = "<img src='./image/finland.png' class='flag'/>";
                        else if (strtoupper($safe_element)=="SPAIN")
                        $safe_element = "<img src='./image/spain.png' class='flag'/>";
                        else if (strtoupper($safe_element)=="BRAZIL")
                        $safe_element = "<img src='./image/brazil.png' class='flag'/>";
                        else{$safe_element = strtoupper($safe_element);}
                    }
                    print("<td>$safe_element</td>");
                }
                print("</tr>");
            }
        }
        echo  "</table></div><div class='col-md-1 content'></div></div>";
    }

    else if($input_error === false) {
        echo "<tr class='show'><td colspan='10' class='search'>No searching result.</td></tr>";
        echo  "</table></div><div class='col-md-1 content'></div></div>";
    }
};
?>

<!--reset-->
<?php
    if(isset($_POST["reset"])){
    echo "<div class='row content'>
        <div class='col-md-12'></div></div>";
    //copy the original data from copy.txt to data.txt to refresh the data file
    $data_file_name = 'data.txt';
    $original_file_name = 'copy.txt';
    $copy_pointer = fopen($original_file_name, 'r');

    if (!$copy_pointer) {
        print( 'error' );
        exit;
    }

    $championships = array();
    while(!feof($copy_pointer)){
        $line = fgets($copy_pointer);
        $lines[] = $line;
    };

    unset ( $line );
    fclose( $copy_pointer );
    if ( ! is_array( $championships ) ) {
            print("<p>There was an error reading the file $data_file_name</p>");
            exit;
        }

    $data_pointer = fopen($data_file_name, 'w');


    $content = implode( "", $lines );
    fputs($data_pointer, $content );
    $closed = fclose( $data_pointer );
    if( $closed ) {
        print '<p class="respond">Reset the data file.</p>';
    }
    else {
        print '<p class="respond">Reset failed</p>';
    }

}

?>

    <div class="row menu">
        <div class="col-md-1"></div>
        <div class="col-md-5">Add a New Champion</div>
        <div class="col-md-5">Search for a Champion</div>
    </div>

    <div class="row content">
        <div class="col-md-1"></div>

        <!--left part of the index-->
        <div class="col-md-5">
        <form method="post">
            <table class="add">
            <tr class="add">
                <td>Season: </td>
                <td><input type="text" name="season" /></td>
                <td>(Please input year within 1960-2017)</td>
            </tr>
            <tr class="add">
                <td>Driver: </td>
                <td><input type="text" name="driver" /></td>
                <td></td>
            </tr>
            <tr class="add">
                <td>Country: </td>
                <td><input type="text" name="nationality" /></td>
                <td></td>
            </tr>
            <tr class="add">
                <td>Car No.: </td>
                <td><input type="text" name="car" /></td>
                <td></td>
            </tr>
            <tr class="add">
                <td>Team: </td>
                <td><select name="team" class="button">
                        <option value="Ferrari">Ferrari</option>
                        <option value="McLaren">McLaren</option>
                        <option value="Mercedes">Mercedes</option>
                        <option value="Red Bull">Red Bull</option>
                        <option value="Williams">Williams</option>
                        <option value="Renault">Renault</option>
                        <option value="Indian Force">Indian Force</option>
                    </select></td>
                <td></td>
            </tr>
            <tr class="add">
                <td>Poles: </td>
                <td><input type="text" name="poles" /></td>
                <td>(Optional)</td>
            </tr>
            <tr class="add">
                <td>Wins: </td>
                <td><input type="text" name="wins" /></td>
                <td>(Optional)</td>
            </tr>
            <tr class="add">
                <td>Podiums: </td>
                <td><input type="text" name="podiums" /></td>
                <td>(Optional)</td>
            </tr>
            <tr class="add">
                <td>Fastest Laps: </td>
                <td><input type="text" name="laps" /></td>
                <td>(Optional)</td>
            </tr>
            <tr class="add">
                <td>Points: </td>
                <td><input type="text" name="points" /></td>
                <td>(Optional)</td>
            </tr>
            <tr class="add">
                <td></td>
                <td><input type="submit" name="add" value="Add" class="sub"/></td>
                 <td></td>

            </tr>
            </table>
        </form>
        </div>

        <!--right part of the index-->
        <div class="col-md-5">
        <form method="post">
            <table class="add">
            <tr class="add">
                <td>Season: </td>
                <td><input type="text" name="season_search" /></td>
                <td>(Please input year within 1960-2017)</td>
            </tr>
            <tr class="add">
                <td>Driver: </td>
                <td><input type="text" name="driver_search" /></td>
                <td></td>
            </tr>
            <tr class="add">
                <td>Country: </td>
                <td><input type="text" name="nationality_search" /></td>
                <td></td>
            </tr>
            <tr class="add">
                <td>Team: </td>
                <td><select name="team_search" class="button">
                        <option value="null">Please Select</option>
                        <option value="Ferrari">Ferrari</option>
                        <option value="McLaren">McLaren</option>
                        <option value="Mercedes">Mercedes</option>
                        <option value="Red Bull">Red Bull</option>
                        <option value="Renault">Renault</option>
                        <option value="Williams">Williams</option>
                        <option value="Indian Force">Indian Force</option>
                    </select></td>
                <td></td>
            </tr>
            <tr class="add">
                <td></td>
                <td><input type="submit" name="search" value="Search" class="sub"/></td>
                 <td></td>

            </tr>
        </table>
        <br>
        <input type="submit" name="show_all" value="Show All" class="menu show_all"/>
        <input type="submit" name="hide" value="Hide" class="menu show_all"/>
        <input type="submit" name="reset" value="Reset" class="menu show_all"/>
        </form>
        </div>
    </div>

    <!--image citation-->
    <div class="row content">
        <div class="col-md-1"></div>
        <div class="col-md-10 cite">
            <p>Image from: </p>
            <?php
                $cite = "https://www.mercedesamgf1.com/en/mercedes-amg-f1/amg-f1-wallpaper/";
                if(isset($_POST["show_all"])){

                    $cite.= "; https://en.wikipedia.org/wiki/Germany; https://en.wikipedia.org/wiki/Flag_of_Finland; https://en.wikipedia.org/wiki/Spain#/media/File:Flag_of_Spain.svg; https://en.wikipedia.org/wiki/File:Flag_of_Brazil.svg";
                    }
                echo "<p>$cite</p>";
            ?>
        </div>
    </div>


    <div class="col-md-12 footer">Copyright BY Yue Shi</div>
</div>
</body>
</html>