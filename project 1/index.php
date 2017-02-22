<!DOCTYPE html>
<html>
<head>
<title>YS Website</title>
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
        <div class="col-md-2"></div>
        <div class="col-md-4" id ="photo"></div>
        <div class="col-md-4 content">
        <h3 style = "margin-top:30px;">Yue Shi</h3>
        <p>Cornell University Master Candidate</p>
        <?php
            $info = array(
                "Phone" => "607-262-0333",
                "Email" => "ys764@cornell.edu",
                "Address" => "20 Fairview SQ, Ithaca, NY",
                "Linkedin" => "https://www.linkedin.com/in/yue-shi-a2a643117"
            );

            foreach ($info as $part => $detail) {
                echo "<h4>$part: </h4>";
                echo "<p>$detail</p>";
            }
        ?>

        <br>

        <div class="col-md-2"></div>
        </div>
    </div>


<div class="row content" style="background-color:black; color:white">
<div class="col-md-3"></div>
<div class="col-md-6 "><p style = "text-align: center">Copyright BY Yue Shi</p></div>
</div>

</body>
</html>
