<?php require_once 'config.php';

?>


<html>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-sm navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="images/R2D2Globe.png" alt="" width="100" height="100">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
<!--                <select name="language" id="language">-->
<!--                    <option value="Admin Features" selected>Light Mode</option>-->
<!--                    <option value="action 1">Dark Mode</option>-->
<!--                </select>-->

                <?php
                $adminAccessLevel = 3;
                if (isset($_SESSION["username"])) {
                    echo '
                    <li class="nav-item"><a class="nav-link" href="postUpload.php">Make Post</a></li>
                    <li class="nav-item"><a class="nav-link" href="communityUpload.php">Make Community</a></li>
                    
                    ';
                    if (isset($_SESSION["access_level"])) {
                        if ($_SESSION["access_level"] == $adminAccessLevel) {

                        ?>
                        <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Administrator Functions
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="userList.php">User List</a>
                        <a class="dropdown-item" href="receivedMessages.php">Received Messages</a>
                            <a class="dropdown-item" href="adminEnablePost.php"> Reenable Post</a>
                        </ul></li>
                        <?php
                    }
                    }
                   }
                    ?>
                        <?php
                        $modAccessLevel = 2;
                        if (isset($_SESSION["username"])) {
                        if (isset($_SESSION["access_level"])) {
                        if ($_SESSION["access_level"] == $modAccessLevel) {

                        ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Moderator Functions
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="adminEnablePost.php"> Re-enable Post</a>
                            <a class="dropdown-item" href=".php"> work in progress</a>
                        </ul></li>


                    <?php
                    }
                    }
                } else {
                    echo '
                    <li class="nav-item"><a class="nav-link" href="userRegister.php">Register</a></li>
                    ';
                    echo '
                    <li class="nav-item"><a class="nav-link" href="userLogin.php">Login</a></li>
                    ';


                }
                ?>

            </ul>
        </div>
        <?php
        if (isset($_SESSION["username"])) {
            echo "<div class='alert alert-success d-flex'><span>Welcome, " . $_SESSION["username"] . "<br><a href='userLogout.php'>Logout</a></span></div>";
        }
        ?>
    </div>
</nav>

<!--https://getbootstrap.com/docs/5.0/layout/containers/
15%, 70%, 15% or 10%, 70%, 20%
So, 1.5, 9, 1.5 or 1, 9, 2-->
<!--<div class="container-fluid">-->
<!--    <div class="row">-->
<!--        <div class="col-1 bg-light p-3 border">-->
<!--            Communities n stuff-->
<!--            <!-- Tested around with making columns inside these containers. Nothing I tried (admittedly not a lot) worked -->
<!--        </div>-->
<!--        <!-- Currently, these sit in the centre rather than touching the edges as I would have liked -->
<!--        <div class="col-9 bg-light p-3 border">-->
<!--            The feed-->
<!--        </div>-->
<!--        <div class="col-2 bg-light p-3 border">-->
<!--            User stuff-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->




<?php
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
//    echo $message;
    ?>
    <div class="position-absolute bottom-0 end-0">
        <?= $message ?>

    </div>


    <?php
}
?>
<script src="js/bootstrap.bundle.js"></script>
<?php
function sanitise_data($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function authorisedAccess($unauthorisedUsers, $users, $admin)
{
    // Unauthenticated User
    if (!isset($_SESSION["username"])) { // user not logged in
        if ($unauthorisedUsers == false) {
            $_SESSION['flash_message'] = "<div class='bg-danger'>Access Denied</div>";
            return false;
        }
    } else {

        // Regular User
        if ($_SESSION["access_level"] == 1) {
            if ($users == false) {
                $_SESSION['flash_message'] = "<div class='bg-danger'>Access Denied</div>";
                return false;
            }
        }

        // Administrators
        if ($_SESSION["access_level"] == 2) {
            if ($admin == false) {
                return false;
            }
        }
    }

    // otherwise, let them through
    return true;
}
function isEnabled($conn)
{
    //Need to pass conn var otherwise fucntion will not work.
    //Finds out the users Enabled value and checks to see if they are legal users.
    /** @var $conn */
    $useridenablecheck = $_SESSION["user_id"];
    $isEnabledQuery = $conn->prepare("SELECT enabled FROM Users WHERE UserID = :user_id");
    $isEnabledQuery->bindParam(":user_id", $useridenablecheck);
    $isEnabledQuery->execute();
    $isEnabled = $isEnabledQuery->fetchColumn();

    if ($isEnabled === false) {
        header("Location:userRegister.php");
    } else {
        if ($isEnabled == 1) {
            //Do nothing if user is enabled.
        } else {
            header("Location:userLogout.php");
        }
    }

}