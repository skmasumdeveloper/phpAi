<!-- ////////php main functions///////// -->
<?php
//error_reporting(0);

# Insert Data 
function Insert($table, $data)
{

    global $mysqli;
    //print_r($data);

    $fields = array_keys($data);
    $values = array_map(array($mysqli, 'real_escape_string'), array_values($data));

    //echo "INSERT INTO $table(".implode(",",$fields).") VALUES ('".implode("','", $values )."');";
    //exit;  
    mysqli_query($mysqli, "INSERT INTO $table(" . implode(",", $fields) . ") VALUES ('" . implode("','", $values) . "');") or die(mysqli_error($mysqli));
}

// Update Data, Where clause is left optional
function Update($table_name, $form_data, $where_clause = '')
{
    global $mysqli;
    // check for optional where clause
    $whereSQL = '';
    if (!empty($where_clause)) {
        // check to see if the 'where' keyword exists
        if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
            // not found, add key word
            $whereSQL = " WHERE " . $where_clause;
        } else {
            $whereSQL = " " . trim($where_clause);
        }
    }
    // start the actual SQL statement
    $sql = "UPDATE " . $table_name . " SET ";

    // loop and build the column /
    $sets = array();
    foreach ($form_data as $column => $value) {
        $sets[] = "`" . $column . "` = '" . $value . "'";
    }
    $sql .= implode(', ', $sets);

    // append the where statement
    $sql .= $whereSQL;

    // run and return the query result
    return mysqli_query($mysqli, $sql);
}


//Delete Data, the where clause is left optional incase the user wants to delete every row!
function Delete($table_name, $where_clause = '')
{
    global $mysqli;
    // check for optional where clause
    $whereSQL = '';
    if (!empty($where_clause)) {
        // check to see if the 'where' keyword exists
        if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
            // not found, add keyword
            $whereSQL = " WHERE " . $where_clause;
        } else {
            $whereSQL = " " . trim($where_clause);
        }
    }
    // build the query
    $sql = "DELETE FROM " . $table_name . $whereSQL;

    // run and return the query result resource
    return mysqli_query($mysqli, $sql);
}

// clean input
function cleanInput($input)
{
    $input = htmlentities(addslashes(trim($input)));
    return $input;
}

// get image from url
function grab_image($file_url, $save_to)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $file_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 140);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    $output = curl_exec($ch);
    $file = fopen($save_to, "w+");
    fputs($file, $output);
    fclose($file);
}


// Get base url
function getBaseUrl($array = false)
{
    $protocol = "http";
    $host = "";
    $port = "";
    $dir = "";

    // Get protocol
    if (array_key_exists("HTTPS", $_SERVER) && $_SERVER["HTTPS"] != "") {
        if ($_SERVER["HTTPS"] == "on") {
            $protocol = "https";
        } else {
            $protocol = "http";
        }
    } elseif (array_key_exists("REQUEST_SCHEME", $_SERVER) && $_SERVER["REQUEST_SCHEME"] != "") {
        $protocol = $_SERVER["REQUEST_SCHEME"];
    }

    // Get host
    if (array_key_exists("HTTP_X_FORWARDED_HOST", $_SERVER) && $_SERVER["HTTP_X_FORWARDED_HOST"] != "") {
        $host = trim(end(explode(',', $_SERVER["HTTP_X_FORWARDED_HOST"])));
    } elseif (array_key_exists("SERVER_NAME", $_SERVER) && $_SERVER["SERVER_NAME"] != "") {
        $host = $_SERVER["SERVER_NAME"];
    } elseif (array_key_exists("HTTP_HOST", $_SERVER) && $_SERVER["HTTP_HOST"] != "") {
        $host = $_SERVER["HTTP_HOST"];
    } elseif (array_key_exists("SERVER_ADDR", $_SERVER) && $_SERVER["SERVER_ADDR"] != "") {
        $host = $_SERVER["SERVER_ADDR"];
    }
    //elseif(array_key_exists("SSL_TLS_SNI", $_SERVER) && $_SERVER["SSL_TLS_SNI"] != "") { $host = $_SERVER["SSL_TLS_SNI"]; }

    // Get port
    if (array_key_exists("SERVER_PORT", $_SERVER) && $_SERVER["SERVER_PORT"] != "") {
        $port = $_SERVER["SERVER_PORT"];
    } elseif (stripos($host, ":") !== false) {
        $port = substr($host, (stripos($host, ":") + 1));
    }
    // Remove port from host
    $host = preg_replace("/:\d+$/", "", $host);

    // Get dir
    if (array_key_exists("SCRIPT_NAME", $_SERVER) && $_SERVER["SCRIPT_NAME"] != "") {
        $dir = $_SERVER["SCRIPT_NAME"];
    } elseif (array_key_exists("PHP_SELF", $_SERVER) && $_SERVER["PHP_SELF"] != "") {
        $dir = $_SERVER["PHP_SELF"];
    } elseif (array_key_exists("REQUEST_URI", $_SERVER) && $_SERVER["REQUEST_URI"] != "") {
        $dir = $_SERVER["REQUEST_URI"];
    }
    // Shorten to main dir
    if (stripos($dir, "/") !== false) {
        $dir = substr($dir, 0, (strripos($dir, "/") + 1));
    }

    // Create return value
    if (!$array) {
        if ($port == "80" || $port == "443" || $port == "") {
            $port = "";
        } else {
            $port = ":" . $port;
        }
        return htmlspecialchars($protocol . "://" . $host . $port . $dir, ENT_QUOTES);
    } else {
        return ["protocol" => $protocol, "host" => $host, "port" => $port, "dir" => $dir];
    }
}

//GCM function
define('APP_GCM_KEY', '27663732gfg');
function Send_GCM_msg($registration_id, $datatitle, $datamsgs)
{
    $datatitle = $datatitle;
    $datamsgs = $datamsgs;

    $registatoin_ids = array($registration_id);
    $fcmMsg = array(
        'title' => $datatitle,
        'body' => $datamsgs,
        'sound' => "default",
        'color' => "#203E78"
    );
    $fcmFields = array(
        'to' => implode($registatoin_ids),
        'priority' => 'high',
        'notification' => $fcmMsg
    );
    $headers = array(
        'Authorization: key=' . APP_GCM_KEY . '',
        'Content-Type: application/json'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
    $result = curl_exec($ch);

    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    // echo $result;
}


//Image compress
function compress_image($source_url, $destination_url, $quality)
{

    $info = getimagesize($source_url);

    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source_url);

    elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($source_url);

    elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($source_url);

    imagejpeg($image, $destination_url, $quality);
    return $destination_url;
}

//Create Thumb Image

function create_thumb_image($target_folder = '', $thumb_folder = '', $thumb_width = '', $thumb_height = '')
{
    //folder path setup
    $target_path = $target_folder;
    $thumb_path = $thumb_folder;

    $thumbnail = $thumb_path;
    $upload_image = $target_path;

    $file_ext = "";

    list($width, $height) = getimagesize($upload_image);
    $thumb_create = imagecreatetruecolor($thumb_width, $thumb_height);
    switch ($file_ext) {
        case 'jpg':
            $source = imagecreatefromjpeg($upload_image);
            break;
        case 'jpeg':
            $source = imagecreatefromjpeg($upload_image);
            break;
        case 'png':
            $source = imagecreatefrompng($upload_image);
            break;
        case 'gif':
            $source = imagecreatefromgif($upload_image);
            break;
        default:
            $source = imagecreatefromjpeg($upload_image);
    }
    imagecopyresized($thumb_create, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
    switch ($file_ext) {
        case 'jpg' || 'jpeg':
            imagejpeg($thumb_create, $thumbnail, 80);
            break;
        case 'png':
            imagepng($thumb_create, $thumbnail, 80);
            break;
        case 'gif':
            imagegif($thumb_create, $thumbnail, 80);
            break;
        default:
            imagejpeg($thumb_create, $thumbnail, 80);
    }
}

function thousandsNumberFormat($num)
{

    if ($num > 1000) {

        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array(' K', ' M', ' B', ' T');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;
    }

    return $num;
}

function calculate_time_span($post_time, $flag = false)
{
    if ($post_time != '') {

        $seconds = time() - $post_time;
        $year = floor($seconds / 31556926);
        $months = floor($seconds / 2629743);
        $week = floor($seconds / 604800);
        $day = floor($seconds / 86400);
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours * 3600)) / 60);
        $secs = floor($seconds % 60);

        if ($seconds < 60) $time = $secs . " sec ago";
        else if ($seconds < 3600) $time = ($mins == 1) ? $mins . " min ago" : $mins . " mins ago";
        else if ($seconds < 86400) $time = ($hours == 1) ? $hours . " hour ago" : $hours . " hours ago";
        else if ($seconds < 604800) $time = ($day == 1) ? $day . " day ago" : $day . " days ago";
        else if ($seconds < 2629743) $time = ($week == 1) ? $week . " week ago" : $week . " weeks ago";
        else if ($seconds < 31556926) $time = ($months == 1) ? $months . " month ago" : $months . " months ago";
        else $time = ($year == 1) ? $year . " year ago" : $year . " years ago";

        if ($flag) {
            if ($day > 1) {
                $time = date('d-m-Y', $post_time);
            }
        }

        return $time;
    } else {
        return 'not available';
    }
}


?>


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