<?php
session_start();
if($_SESSION['admin'] != TRUE){
    header('Location: 404.php');
}
require_once "config.php";
$conn = new PDO("mysql:host=".HOST.";dbname=".DBNAME, USERNAME, PASSWORD);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($_POST['action'] == "addMember"){
    $csv = $_POST['memberInput'];
    $members = explode("\n", $csv);
    $info = array();


    $stmt = $conn->prepare("INSERT INTO members (full_name, email, rand_int) VALUES (:full_name, :email, :rand_int)");

    $stmt->bindParam(':full_name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':rand_int', $rand);

    foreach($members as $member){
        $member_info = explode(",", $member);
        $name = $member_info[0];
        $email = $member_info[1];
        $bytes = random_bytes(8);
        $rand = bin2hex($bytes);

        $stmt->execute();
    }

}
else if($_POST['action'] == "pairUp"){



    $result = $conn->query("SELECT * FROM members ORDER BY full_name");
    if ($result) {
        $members = array();
        $pairs = array();
        $i = 0;
        while($row = $result->fetch()) {
            $mid = $row['mid'];
            $members[$i] = $mid;
            $pairs[$mid] = null;
            $i++;
        }

        $n = count($members);

        for($j = 0; $j < $n; $j++){
            $rand = random_int(0, $n-1);
            while($pairs[$members[$rand]] != null || $rand == $j){
                $rand = random_int(0, $n-1);
            }
            $pairs[$members[$rand]] = $members[$j];
        }

        $conn->query("TRUNCATE pairs");

        $stmt = $conn->prepare("INSERT INTO pairs (mfrom, mto) VALUES (:mfrom, :mto)");
        $stmt->bindParam(':mfrom', $mfrom);
        $stmt->bindParam(':mto', $mto);

        foreach($pairs as $mfrom => $mto){
            $stmt->execute();
        }

    }
}
else if($_POST['action'] == "email"){
    $result = $conn->query("SELECT * FROM members ORDER BY full_name");
    while($row = $result->fetch()) {
        $name = $row['full_name'];
        $id = $row['rand_int'];
        $to = trim($row['email']);
        $subject = "CRT Secret Santa";
        $txt = <<<EOD
Hello $name!

Thank you for participating in Cornell Rocketry's Secret Santa 2016!
The gift exchange will be happening during our last meeting on 12/4.
The budget for each gift is $5 to $20. Hopefully, that doesn't break the bank for any of you.
You can find your pairing through the link below. Be sure to fill out the questionaire as well so that your partner know what to get you as well.

http://cornellrocketryteam.com/secretSanta/user.php?id=$id

If you have any questions/concerns, email me at my regular address: tc464@cornell.edu.

Happy Holidays!
Tomasz
CRT Business Lead
EOD;
        $headers = "From: santa@cornellrocketryteam.com";

        var_dump($to);
        mail($to, $subject, $txt, $headers);

    }
}

?>

<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->

        <link rel="stylesheet" href="css/normalize.css">
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <link rel="stylesheet" href="css/skeleton.css">
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body>
        <div class="container">
            <div class="row heading centered">
                <div class="offset-by-three six columns">
                    <h3>CRT Secret Santa</h3>
                    <h4>Admin Controls</h4>
                </div>
            </div>
            <form action="home.php" method="post">
                <input type="hidden" name="action" value="addMember">
                <div class="row">
                    <label for="memberInput">Add New Members</label>
                    <textarea class="u-full-width" placeholder="Tomasz Chmielewski,tc464@cornell.edu" name="memberInput" id="memberInput" rows="10"></textarea>
                </div>
                <div class="row">
                    <input class="button-primary" type="submit" value="Add Members">
                </div>
            </form>

            <div class="row">
                List of Members
                <br>
                <button class="button-primary" id="showTableButton">Show</button>
                <button class="hidden" id="hideTableButton">Hide</button>
            </div>
            <div class="row">


                <table class="u-full-width hidden" id="memberTable">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Giving to</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM members ORDER BY full_name");
                    if ($result) {
                        while($row = $result->fetch()) {
                            $name = $row['full_name'];
                            $email = $row['email'];
                            $rand = $row['rand_int'];
                            $mid = $row['mid'];

                            $result2 = $conn->query("SELECT * FROM members INNER JOIN pairs ON mid=mto WHERE mfrom = $mid");
                            $row2 = $result2->fetch();
                            $name2 = $row2['full_name'];
                            $rand2 = $row2['rand_int'];

                            echo <<<EOD
                                <tr>
                                    <td><a href="user.php?id=$rand">$name</a></td>
                                    <td>$email</td>
                                    <td><a href="user.php?id=$rand2">$name2</a></td>
                                </tr>
EOD;
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <form action="home.php" method="post">
                <input type="hidden" name="action" value="pairUp">
                <div class="row">
                    Generate new pairs
                </div>
                <div class="row">
                    <input class="button-primary" type="submit" value="Generate">
                </div>
            </form>

            <form action="home.php" method="post">
                <input type="hidden" name="action" value="email">
                <div class="row">
                    Send email
                </div>
                <div class="row">
                    <input class="button-primary" type="submit" value="Email">
                </div>
            </form>

        </div>

        <script
                src="https://code.jquery.com/jquery-3.1.1.min.js"
                integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
                crossorigin="anonymous"></script>
        <script src="js/main.js"></script>
    </body>
</html>
