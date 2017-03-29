<?php include('script/header.php'); ?>
<div class="container">

<?php

    $msg = "This website is the archive of winners for each Formula One Grand Prix. Please log in.";


    if(isset($_SESSION['logged_user_by_sql'])){
        echo "<div class='row content'><div class='col-md-3'></div><div class='col-md-6'>
        <p>Hello, {$_SESSION['logged_user_by_sql']}. You are now getting access to the edit/add function.</p>
        <p>Please log out after your visit.</p></div></div>";
?>
    <div class="row">
        <div class="col-md-6"></div>
        <form  method="post">
            <div class="col-md-3"><input type="submit" value="LOG OUT" name="log_out" class="log_in"/></div>
        </form>
    </div>

<?php

    }
    if(isset($_SESSION['logged_result']) && !$_SESSION['logged_result']){
        $msg = "You did not login successfully. Please try again.";
    }

    if(!isset($_SESSION['logged_user_by_sql'])){
        echo "<div class='row content'><div class='col-md-3'></div><div class='col-md-6'><p>$msg</p></div></div>";

?>

<div class="row content">
    <div class="col-md-3"></div>
    <form action="index.php" method="post">
        <div class="col-md-3">
            <table class="log">
            <tr class="log">
                <td class="item">Username: </td>
                <td class="log"><input type="text" name="user" /></td>
            </tr>
            <tr class="log">
                <td class="item">Password: </td>
                <td class="log"><input type="password" name="password" /></td>
            </tr>
           </table>
        </div>
        <div class="col-md-3"><input type="submit" value="LOG IN" class="log_in"/></div>
    </form>
</div>

<?php
}
?>

<div class="col-md-12 footer">Copyright BY Yue Shi</div>
</div>
</body>
</html>