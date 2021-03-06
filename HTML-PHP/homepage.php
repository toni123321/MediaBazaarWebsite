
<?php

include_once '../Logic/EmployeeManager.class.php';
include_once '../Handling/timeUntilNextShift.php';
?>
<?php

if(isset($_SESSION['loggedUserId']))
{
    $employeeManager = new EmployeeManager();
    $loggedUserId = (int) $_SESSION['loggedUserId'];
    $currEmp = $employeeManager->GetEmployee($loggedUserId);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="600" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/homepage-style.css">
    <script src="../Libraries/jquery-3.6.0.min.js"></script>
    <title>Home</title>
</head>
<body>
    <?php include 'main.php';?>
    <div class="content">
        <div class="welcome-text">
            <h2>Welcome, <?php echo $currEmp->GetFirstName(); ?></h2>
            <?php
                if(isset($_SESSION['nextShiftTime'])) {
                    $nextShift = unserialize($_SESSION['nextShiftTime']);
                    echo "<p>Your next shift starts at {$nextShift->format('G:i')} on {$nextShift->format('j M, Y')} ({$nextShift->format('l')})</p>";
                }
                else{
                    echo '<p>There are no upcoming shifts. Have fun!</p>';
                }
            ?>

        </div>
        <div class="btnview">
            <form id="viewSchedule" class="viewSchedule" action="../HTML-PHP/schedule.php" method="post">
                <button type="submit">View full schedule</button>
            </form>
        </div>
        <?php
        if (isset($_SESSION['nextShiftId'])) {
            date_default_timezone_set('Europe/Amsterdam');

            $nextShiftId = (int)($_SESSION['nextShiftId']);
            $DbHelper = new DbHelper();
            $shifts = $DbHelper->GetShifts();
            $nextShift = null;

            foreach ($shifts as $shift){
                if($shift->GetId() == $nextShiftId){
                    $nextShift = $shift;
                }
            }

            $nextShiftTime = unserialize($_SESSION['nextShiftTime']);
            $nextShiftTime2 = unserialize($_SESSION['nextShiftTime']);

            $dateNowString = date("Y-m-d H:i:s");
            $dateNow = new DateTime($dateNowString);

            $earlyCheckIn = $nextShiftTime->modify('-15 minutes');
            $lateCheckIn = $nextShiftTime2->modify('+5 minutes');

            $earlyCheckInSTR = $earlyCheckIn->format("Y-m-d H:i:s");
            $lateCheckInSTR =  $lateCheckIn->format("Y-m-d H:i:s");

            if($dateNowString > $earlyCheckInSTR && $dateNowString < $lateCheckInSTR && $nextShift->GetHasAttended() == false) {
               echo '
            <div class="btnview">
                <form id="viewSchedule" class="viewSchedule checkInBtn" action="../Handling/checkInHandling.php" method="post">
                    <button type="submit">Check in</button>
                </form>
            </div>';
            }
            else if($dateNowString > $earlyCheckInSTR && $dateNowString < $lateCheckInSTR && $nextShift->GetHasAttended() == true){
                    echo '
            <div class="btnview">
                <form id="viewSchedule" class="viewSchedule checkInBtn" action="../Handling/checkInHandling.php" method="post">
                    <button class="alreadyChecked" type="submit" disabled>Already checked in</button>
                </form>
            </div>';
            }
        }

        ?>
    </div>

    <?php include '../HTML-PHP/footer.php'; ?>
    <!--<script src="../JavaScript/autoRefreshHomepage.js"></script>-->
    <script src="../JavaScript/processProgressBar.js"></script>

</body>
</html>
<?php
}
else{
    header("Location: ../HTML-PHP/landing-login.php");
}
?>
