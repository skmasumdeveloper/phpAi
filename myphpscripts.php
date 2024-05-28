

<!-- ///////// login -->
<?php

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

if ($username == "") {
    $_SESSION['msg'] = "1";
    header("Location:../login");
    exit;
} else if ($password == "") {
    $_SESSION['msg'] = "2";
    header("Location:../login");
    exit;
} else {
    $qry = "select * from tbl_users where (email='" . $username . "' and password='" . $password . "') or (phone='" . $username . "' and password='" . $password . "') ";
    $result = mysqli_query($mysqli, $qry);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if ($row['status'] == 0) {

            $_SESSION['msg'] = "22";
            header("Location:../login");
            exit;
        } else {
            $user_logged = "INSERT INTO user_log (userid) VALUES ('" . $row['id'] . "')";
            $logdataresult = mysqli_query($mysqli, $user_logged);

            $cookie_name1 = "userid";
            $cookie_value1 = $row['id'];
            setcookie($cookie_name1, $cookie_value1, time() + (86400 * 300), "/"); // 86400 = 1 day

            $cookie_name2 = "user";
            $cookie_value2 = $row['name'];
            setcookie($cookie_name2, $cookie_value2, time() + (86400 * 300), "/"); // 86400 = 1 day

            $_SESSION['id'] = $_COOKIE[$cookie_name1];
            $_SESSION['user_name'] = $_COOKIE[$cookie_name2];

            header("Location:../home");
            exit;
        }
    } else {
        $_SESSION['msg'] = "4";
        header("Location:../login");
        exit;
    }
}

?>

<!-- ///////// Logout -->
<?php
session_start();
unset($_SESSION["user_name"]);
unset($_SESSION['id']);

$cookie_name1 = "userid";
$cookie_name2 = "user";

setcookie($cookie_name1, '', time() + (86400 * 300), "/");
setcookie($cookie_name2, '', time() + (86400 * 300), "/");

session_destroy();
echo "<script language=javascript>location.href='../index.php';</script>";
?>

<!-- ////////// Register -->
<?php

if (isset($_POST['name'])) {

    $quser_qry = "SELECT * FROM tbl_users where email='" . $_POST['email'] . "' OR phone='" . $_POST['phone'] . "' ";
    $u_result = mysqli_query($mysqli, $quser_qry) or die(mysqli_error($mysqli));
    $qcount = mysqli_num_rows($u_result);
    if ($qcount > 0) {
        $_SESSION['msg'] = "28";
        header("location:signup");
        exit;
    } else {
        $row = mysqli_fetch_assoc($u_result);

        $name = cleanInput($_POST['name']);
        $middle_name = cleanInput($_POST['middle_name']);
        $last_name = cleanInput($_POST['last_name']);
        $phone = cleanInput($_POST['phone']);
        $email = cleanInput($_POST['email']);
        $password = cleanInput($_POST['password']);

        $data = array(
            'user_type' => "Normal",
            'name' => $name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'phone'  =>  $phone,
            'email'  =>  $email,
            'password'  =>  $password,
            'register_on'  =>  strtotime(date('d-m-Y h:i:s A')),
            'status'  =>  1,

        );

        $qry = Insert('tbl_users', $data);
        $_SESSION['msg'] = "23";

        $user_logged = "INSERT INTO user_log (userid) VALUES ('" . $row['id'] . "')";
        $logdataresult = mysqli_query($mysqli, $user_logged);

        $cookie_name1 = "userid";
        $cookie_value1 = $row['id'];
        setcookie($cookie_name1, $cookie_value1, time() + (86400 * 300), "/"); // 86400 = 1 day

        $cookie_name2 = "user";
        $cookie_value2 = $row['name'];
        setcookie($cookie_name2, $cookie_value2, time() + (86400 * 300), "/"); // 86400 = 1 day

        $_SESSION['id'] = $_COOKIE[$cookie_name1];
        $_SESSION['user_name'] = $_COOKIE[$cookie_name2];

        echo '<script type="text/javascript">
                window.location.href="home";
              </script>';
        exit;
    }
}

?>

<!-- /////////// Insert Data -->
<?php
if (isset($_POST['formname'])) {
    $name = cleanInput($_POST['name']);
    $middle_name = cleanInput($_POST['middle_name']);

    $data = array(

        'name' => $name,
        'middle_name' => $middle_name,
        'date'  =>  date('d-m-Y'),
        'time'  =>  date('H:i:s'),
        'status'  =>  1,

    );

    $qry = Insert('tbl_users', $data);
    $_SESSION['msg'] = "23";
    echo '<script type="text/javascript">
                window.location.href="pagename.php";
              </script>';
    exit;
}
?>

<!-- /////////// Insert Data with Image -->
<?php
if (isset($_POST['formname'])) {
    $name = cleanInput($_POST['name']);
    $middle_name = cleanInput($_POST['middle_name']);

    $img_cover = rand(0, 99999) . $_FILES['photo']['name'];
    $pic1cover = $_FILES['photo']['tmp_name'];
    $tpath1cover = 'uploads/images/' . $img_cover;
    copy($pic1cover, $tpath1cover);

    $data = array(

        'name' => $name,
        'middle_name' => $middle_name,
        'image' => $img_cover,
        'date'  =>  date('d-m-Y'),
        'time'  =>  date('H:i:s'),
        'status'  =>  1,

    );

    $qry = Insert('tbl_users', $data);
    $_SESSION['msg'] = "23";
    echo '<script type="text/javascript">
                window.location.href="pagename.php";
              </script>';
    exit;
}
?>

<!-- //////////// Update Data -->
<?php
if (isset($_POST['formname'])) {
    $data = array(
        'read_status'  =>  0,
    );

    $data_update = Update('tbl_name', $data, "WHERE id = '" . $id . "'");
    $_SESSION['msg'] = "23";
    echo '<script type="text/javascript">
                window.location.href="pagename.php";
              </script>';
    exit;
}
?>

<!-- //////////// Delete Data -->
<?php

$data_update = Delete('tbl_name', "WHERE id = '" . $id . "'");
$_SESSION['msg'] = "23";
echo '<script type="text/javascript">
                window.location.href="pagename.php";
              </script>';
exit;
?>

<!-- ////// fetch single row data  -->
<?php

$qry = "select * from tbl_users where id='" . $id . "'";
$result = mysqli_query($mysqli, $qry);
$row = mysqli_fetch_assoc($result);

?>

<!-- ////// fetch single row data count number -->
<?php

$qry = "select * from tbl_users where id='" . $id . "'";
$result = mysqli_query($mysqli, $qry);
$data_count = $result->num_rows;

?>


<!-- ////// fetch dynamic multi row data  -->
<?php
$data_qry = "SELECT * FROM tbl_name WHERE status=1 ORDER BY id DESC";
$data_result = mysqli_query($mysqli, $data_qry);
$i = 0;
while ($data_row = mysqli_fetch_array($data_result)) {
?>

    <span>
        serial no - <?= $i++; ?><br>
        id - <?= $data_row['id']; ?><br>
        title - <?= $data_row['title']; ?><br>
        date - <?= date_format(date_create($data_row['date']), "d M, Y") ?>
    </span>

<?php } ?>



<!-- ////// fetch dynamic multi row data  -->
<?php
$data_qry = "SELECT * FROM tbl_name WHERE status=1 ORDER BY id DESC";
$data_result = mysqli_fetch_array(mysqli_query($mysqli, $data_qry));
$i = 0;
foreach ($data_result as $data_row) {
?>

    <span>
        serial no - <?= $i++; ?><br>
        id - <?= $data_row['id']; ?><br>
        title - <?= $data_row['title']; ?><br>
        date - <?= date_format(date_create($data_row['date']), "d M, Y") ?>
    </span>

<?php } ?>


<!-- ///////// ajax post ////////// -->

<script>
    $('#sendchat').on('click', function() {
        $("#sendchat").attr("disabled", "disabled");
        var senderid = $('#from-user-id').val();
        var sendtoid = $('#to-user-id').val();
        var sendmessage = $('#text-message').val();
        // alert(sendmessage);

        if (sendmessage != "") {
            $.ajax({
                url: "api/chat.php",
                type: "POST",
                data: {
                    senderid: senderid,
                    sendtoid: sendtoid,
                    sendmessage: sendmessage,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    if (dataResult.statusCode == 200) {
                        $('#text-message').val('');
                        $('#thelastcid').val(dataResult.lastchatid);
                        // alert("Data added successfully !");
                    } else if (dataResult.statusCode == 201) {
                        alert("Error occured !");
                    }
                }
            });
        } else {
            alert('Please fill all the field !');
        }
    });
</script>

<?php echo json_encode(array("statusCode" => 201)); ?>
