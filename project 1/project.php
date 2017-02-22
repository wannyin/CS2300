<!DOCTYPE html>
<html>
<head>
<title>YS Project</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link href="https://fonts.googleapis.com/css?family=Overpass" rel="stylesheet">
</head>
<body class="container">
    <div class="row banner">
        <div class="col-md-3">
        <h2 id = "name"><a href="index.php">Yue (Janice) Shi</a></h2>
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-1 sub"><p><a href="education.html">Education</a></p></div>
        <div class="col-md-1 sub"><p><a href="experience.html">Experience</a></p></div>
        <div class="col-md-1 sub-1"><p><a href="project.php">Project</a></p></div>
    </div>
    <div class="row main" >
        <div class="col-md-1"></div>
        <div class="col-md-3" style="margin-left:20px">
        <div class="rectangle" >
            <img style="margin-top: 55px;" src="./image/walmart.png" alt = "project_1" height=75 />
            </div>
            <h3 class="content" style = "margin-top:10px;">Inventory Optimization based on the Cost of Out-of-Stock</h3>
            <p class="content" style = "margin:0">Nov.2016-Present</p>
            <ul>
            <li class="content" style = "margin:0">Improve a model to take delay purchases and long-term influence of out-of-stock into consideration when managing inventory. </li>
            <li class="content" style = "margin:0">Figure out useful data sources and clean datasets, extract necessary features.</li>

            </ul>
            <p class="content cite">Img from: http://corporate.walmart.com</p>


        </div>
        <div class="col-md-3" style="margin-left:30px">
            <div class="rectangle">
            <img style="margin-top: 15px" src="./image/airbnb.png" alt = "project_1" height=170 />
            </div>
            <h3 class="content" style = "margin-top:10px;">Price Recommendation System for Airbnb Hosts</h3>
            <p class="content" style = "margin:0">Sep.2016-Dec.2016</p>
            <ul>
            <li class="content" style = "margin:0">Applied linear regression, classification to calculate recommended listing price for Airbnb hosts within 40% error under 90% CI.</li>
            <li class="content" style = "margin:0">Identified top 20 influential amenities, including room type, bathroom, duration, through random forest.</li>

            </ul>
            <p class="content cite">Img from: http://databits.io/challenges/airbnb-user-pathways-challenge</p>

        </div>
        <div class="col-md-3" style="margin-left:30px">
        <div class="rectangle">
            <img style="margin-top: 20px; margin-left:50px" src="./image/bg.jpg" alt = "project_1" height=170 /></div>
            <h3 class="content" style = "margin-top:10px;">Parking Solutions for Shanghai Disneyland</h3>
            <p class="content" style = "margin:0">Jan.2015-Jun.2015</p>
            <ul>
            <li class="content" style = "margin:0">Figured out the key parking problems by simulating the traffic system of Disneyland in TSIS.</li>
            <li class="content" style = "margin:0">Cut down the waiting time by 30% and increased overall safety through reallocating parking lots and entrances.</li>

            </ul>
            <p class="content cite">Img from: Shanghai Ministry of Transport</p>

        </div>
    </div>

    <form method="post">
        <div class="row main" style = "padding-top:0">
            <div class="col-md-1"></div>
            <div class="col-md-3" style = "text-align: center;margin-left:30px"><input type="checkbox" name="one" value="yes"/>
            </div>
            <div class="col-md-4" style = "text-align: center"><input type="checkbox" name="two" value="yes" />
            </div>
            <div class="col-md-3" style = "text-align: center;margin-left:30px"><input type="checkbox" name="three" value="yes"/>
            </div>
            <div class="col-md-1"></div>
        </div>

        <div class="row main content" style = "padding-top:0">
            <div class="col-md-1"></div>
            <div class="col-md-10" style = "text-align: center">
                <p>Which project do you like most?<br>
                 Please select the projects that attracks you in the checkboxs above!<br>
                 And it will be really helpful if you can give some comments on any of these project. <br></p>
                 <textarea name="comment" rows="5" cols="40"></textarea>
                 <br>
                <input type="submit" name="submit" value="I like it!" />
            </div>
        </div>
    </form>

    <?php
        $count = array(
                "Project_1" => 30,
                "Project_2" => 40,
                "Project_3" => 50
            );

        function check_comment($comment,$a,$flag){

            if(preg_match("/([Dd]isney)+/", $comment)){
                $a .= "We have received your comment on Disney Project!<br>";
                $flag = True;
            }
            if(preg_match("/([Ww]almart)+/", $comment)){
                $a .= "We have received your comment on Walmart Project!<br>";
                $flag = True;
            }
            if(preg_match("/([Aa]irbnb)+/", $comment)){
                $a .= "We have received your comment on Airbnb Project!<br>";
                $flag = True;
            }
            return array($a,$flag);
        }

        $one = $count['Project_1'];
        $two = $count['Project_2'];
        $three = $count['Project_3'];

        if(isset($_POST['submit'])){
            echo "<div class='row main'><div class='col-md-12' style ='text-align:center;font-weight:bold;'>";
            $a = "";
            if (isset($_POST['one']) && $_POST['one']=='yes') {
                $a .= "There are $one people also like the Walmart Project!<br>";
            }
            if (isset($_POST['two']) && $_POST['two']=='yes') {
                $a .= "There are $two people also like the Airbnb Project!<br>";
            }
            if (isset($_POST['three']) && $_POST['three']=='yes') {
                $a .="There are $three people also like the Disney Project!<br>";
            }
            if (isset($_POST['comment'])) {
                $flag = False;
                $a.="<br>";
                list($a,$flag) = check_comment($_POST["comment"],$a,$flag);
                if ($flag != True)
                    $a .="We have received your comment!<br>";
            }

            echo "$a<br>";
            echo "Thank you for your participant! ^.^ ";
            echo "</div></div>";
        }



    ?>

    <div class="row content" style="background-color:black; color:white">
        <div class="col-md-3"></div>
        <div class="col-md-6 "><p style = "text-align: center">Copyright BY Yue Shi</p></div>
    </div>

</body>
</html>
