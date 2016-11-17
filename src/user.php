<?php
require_once "config.php";
$conn = new PDO("mysql:host=".HOST.";dbname=".DBNAME, USERNAME, PASSWORD);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(is_null($_REQUEST['id'])){
    header('Location: 404.php');
    exit;
}

$rand = $_REQUEST['id'];
$stmt = $conn->prepare("SELECT * FROM members WHERE rand_int=:rand");
$stmt->bindParam(":rand", $rand);
$result = $stmt->execute();

$row = $stmt->fetch();

$name = $row['full_name'];
$mid = $row['mid'];

if(is_null($name)){
    header('Location: 404.php');
    exit;
}

$saved = false;

if($_POST['action'] == "answer"){
    $answers = array($_POST['yearInput'],$_POST['mmInput'],$_POST['clothingInput'],$_POST['colorInput'],$_POST['foodInput'],$_POST['drinkInput'],$_POST['musicInput'],$_POST['freeInput'],$_POST['hobbyInput'],$_POST['giftInput'],$_POST['nnInput']);

    $stmt = $conn->prepare("INSERT INTO questions(qid, mid, answer) VALUES (:qid, :mid, :answer) ON DUPLICATE KEY UPDATE answer=:answer");
    $stmt->bindParam(":qid", $i);
    $stmt->bindParam(":mid", $mid);
    $stmt->bindParam(":answer", $answer);

    for($i=0; $i<count($answers); $i++){
        $answer = $answers[$i];
        $stmt->execute();
    }
    $saved = true;
}

$questions = array("yearInput", "mmInput", "clothingInput", "colorInput", "foodInput", "drinkInput", "musicInput", "freeInput", "hobbyInput", "giftInput", "nnInput");
$answers = array();

$stmt = $conn->prepare("SELECT * FROM questions WHERE mid=:mid ORDER BY qid");
$stmt->bindParam(":mid", $mid);
$stmt->execute();
while($row = $stmt->fetch()){
    $qid = $row['qid'];
    if(is_null($qid)){
        break;
    }
    $answer = $row['answer'];
    if(is_null($answer)){
        $answer = "";
    }
    $answers[$qid] = $answer;
}

$stmt = $conn->prepare("SELECT mto, full_name FROM pairs INNER JOIN members ON mid=mto WHERE mfrom=:mid");
$stmt->bindParam(":mid", $mid);
$stmt->execute();
$row = $stmt->fetch();
$mto = $row["mto"];
$nameto = $row['full_name'];

$answers2 = array();

$stmt = $conn->prepare("SELECT * FROM questions WHERE mid=:mto ORDER BY qid");
$stmt->bindParam(":mto", $mto);
$stmt->execute();
while($row = $stmt->fetch()){
    $qid = $row['qid'];
    if(is_null($qid)){
        break;
    }
    $answer = $row['answer'];
    if(is_null($answer)){
        $answer = "";
    }
    $answers2[$qid] = $answer;
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
                <h3>CRT Secret Santa</h3>
                <h4>Welcome <?php echo $name;?>!</h4>
                <?php
                    if($saved){
                        echo "<span class='saved'>Updated answers!</span>";
                    }
                ?>
            </div>
            <form action="user.php" method="post">
                <input type="hidden" name="id" value="<?php echo $rand;?>">
                <input type="hidden" name="action" value="answer">
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="yearInput">What year are you?</label>
                        <input class="u-full-width" type="text" id="yearInput" name="yearInput" value="<?php echo is_null($answers[0]) ? "" : $answers[0];?>">
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="mmInput">What's your major/minor?</label>
                        <input class="u-full-width" type="text" id="mmInput" name="mmInput" value="<?php echo is_null($answers[1]) ? "" : $answers[1];?>">
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="clothingInput">Clothing Size? (shirt, pants, whatever else)</label>
                        <input class="u-full-width" type="text" id="clothingInput" name="clothingInput" value="<?php echo is_null($answers[2]) ? "" : $answers[2];?>">
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="colorInput">Favorite Color</label>
                        <input class="u-full-width" type="text" id="colorInput" name="colorInput" value="<?php echo is_null($answers[3]) ? "" : $answers[3];?>">
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="foodInput">Favorite food/candy/snack?</label>
                        <input class="u-full-width" type="text" id="foodInput" name="foodInput" value="<?php echo is_null($answers[4]) ? "" : $answers[4];?>">
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="drinkInput">Favorite drink? ;)</label>
                        <input class="u-full-width" type="text" id="drinkInput" name="drinkInput" value="<?php echo is_null($answers[5]) ? "" : $answers[5];?>">
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="musicInput">Favorite artist/band/music?</label>
                        <input class="u-full-width" type="text" id="musicInput" name="musicInput" value="<?php echo is_null($answers[6]) ? "" : $answers[6];?>">
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="freeInput">What do you do in your free time?</label>
                        <textarea class="u-full-width" id="freeInput" name="freeInput" value="<?php echo is_null($answers[7]) ? "" : $answers[7];?>"><?php echo is_null($answers[7]) ? "" : $answers[7];?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="hobbyInput">Any other hobbies?</label>
                        <textarea class="u-full-width" id="hobbyInput" name="hobbyInput" value="<?php echo is_null($answers[8]) ? "" : $answers[8];?>"><?php echo is_null($answers[8]) ? "" : $answers[8];?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="giftInput">Any specific gift that you want? ($5 - $20)</label>
                        <textarea class="u-full-width" id="giftInput" name="giftInput" value="<?php echo is_null($answers[9]) ? "" : $answers[9];?>"><?php echo is_null($answers[9]) ? "" : $answers[9];?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="nnInput">Have you been naughty or nice?</label>
                        <input class="u-full-width" type="text" id="nnInput" name="nnInput" value="<?php echo is_null($answers[10]) ? "" : $answers[10];?>">
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <input class="button-primary" type="submit" value="Update">
                    </div>
                </div>

            </form>

            <div class="row">
                <div class="offset-by-three five columns">
                    You've been matched with...
                </div>
                <div class="one columns">
                    <button class="button-primary" id="showPersonButton">Show</button>
                    <button class="hidden" id="hidePersonButton">Hide</button>
                </div>
            </div>
            <div id="personStuff" class="hidden">
                <div class="row">
                    <div class="offset-by-three six columns">
                        <h5><?php echo $nameto;?></h5>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="yearInput">What year are you?</label>
                        <input class="u-full-width" type="text" id="yearInput" name="yearInput" value="<?php echo is_null($answers2[0]) ? "" : $answers2[0];?>" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="mmInput">What's your major/minor?</label>
                        <input class="u-full-width" type="text" id="mmInput" name="mmInput" value="<?php echo is_null($answers2[1]) ? "" : $answers2[1];?>" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="clothingInput">Clothing Size? (shirt, pants, whatever else)</label>
                        <input class="u-full-width" type="text" id="clothingInput" name="clothingInput" value="<?php echo is_null($answers2[2]) ? "" : $answers2[2];?>" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="colorInput">Favorite Color</label>
                        <input class="u-full-width" type="text" id="colorInput" name="colorInput" value="<?php echo is_null($answers2[3]) ? "" : $answers2[3];?>" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="foodInput">Favorite food/candy/snack?</label>
                        <input class="u-full-width" type="text" id="foodInput" name="foodInput" value="<?php echo is_null($answers2[4]) ? "" : $answers2[4];?>" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="drinkInput">Favorite drink? ;)</label>
                        <input class="u-full-width" type="text" id="drinkInput" name="drinkInput" value="<?php echo is_null($answers2[5]) ? "" : $answers2[5];?>" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="musicInput">Favorite artist/band/music?</label>
                        <input class="u-full-width" type="text" id="musicInput" name="musicInput" value="<?php echo is_null($answers2[6]) ? "" : $answers2[6];?>" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="freeInput">What do you do in your free time?</label>
                        <textarea class="u-full-width" id="freeInput" name="freeInput" value="<?php echo is_null($answers2[7]) ? "" : $answers2[7];?>" readonly><?php echo is_null($answers2[7]) ? "" : $answers2[7];?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="hobbyInput">Any other hobbies?</label>
                        <textarea class="u-full-width" id="hobbyInput" name="hobbyInput" value="<?php echo is_null($answers2[8]) ? "" : $answers2[8];?>" readonly><?php echo is_null($answers2[8]) ? "" : $answers2[8];?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="giftInput">Any specific gift that you want? ($5 - $20)</label>
                        <textarea class="u-full-width" id="giftInput" name="giftInput" value="<?php echo is_null($answers2[9]) ? "" : $answers2[9];?>" readonly><?php echo is_null($answers2[9]) ? "" : $answers2[9];?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="offset-by-three six columns">
                        <label for="nnInput">Have you been naughty or nice?</label>
                        <input class="u-full-width" type="text" id="nnInput" name="nnInput" value="<?php echo is_null($answers2[10]) ? "" : $answers2[10];?>" readonly>
                    </div>
                </div>
            </div>
        </div>



        <script
                src="https://code.jquery.com/jquery-3.1.1.min.js"
                integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
                crossorigin="anonymous"></script>
        <script src="js/user.js"></script>
    </body>
</html>
