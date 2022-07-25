<?php //error_reporting(0);



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

function checkSignSalt($data_info)
{

    $key = "viaviweb";

    $data_json = $data_info;

    $data_arr = json_decode(urldecode(base64_decode($data_json)), true);


    if ($data_arr['sign'] == '' && $data_arr['salt'] == '') {
        //$data['data'] = array("status" => -1, "message" => "Invalid sign salt.");

        $set['LIVETV'][] = array("status" => -1, "message" => "Invalid sign salt.");
        header('Content-Type: application/json; charset=utf-8');
        echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        exit();
    } else {

        $data_arr['salt'];

        $md5_salt = md5($key . $data_arr['salt']);

        if ($data_arr['sign'] != $md5_salt) {

            //$data['data'] = array("status" => -1, "message" => "Invalid sign salt.");
            $set['LIVETV'][] = array("status" => -1, "message" => "Invalid sign salt.");
            header('Content-Type: application/json; charset=utf-8');
            echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            exit();
        }

        if (isset($data_arr['user_id']) || $data_arr['user_id'] != '') {

            global $mysqli;

            $user_id = $data_arr['user_id'];

            $sql = "SELECT * FROM tbl_users WHERE `id`='$user_id' AND `status`='1'";
            $res = mysqli_query($mysqli, $sql);

            if (mysqli_num_rows($res) > 0) {

                mysqli_free_result($res);

                $sql = "SELECT * FROM tbl_active_log WHERE `user_id`='$user_id'";
                $res = mysqli_query($mysqli, $sql);

                if (mysqli_num_rows($res) == 0) {
                    // insert active log

                    $data_log = array(
                        'user_id'  =>  $user_id,
                        'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
                    );

                    $qry = Insert('tbl_active_log', $data_log);
                } else {
                    // update active log
                    $data_log = array(
                        'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
                    );

                    $update = Update('tbl_active_log', $data_log, "WHERE user_id = '$user_id'");
                }

                mysqli_free_result($res);
            }
        }
    }

    return $data_arr;
}

function getTimeDifference($time)
{
    //Let's set the current time
    $currentTime = date('Y-m-d H:i:s');
    $toTime = strtotime($currentTime);

    //And the time the notification was set
    $fromTime = strtotime($time);

    //Now calc the difference between the two
    $timeDiff = floor(abs($toTime - $fromTime) / 60);

    //Now we need find out whether or not the time difference needs to be in
    //minutes, hours, or days
    if ($timeDiff < 2) {
        $timeDiff = "Just now";
    } elseif ($timeDiff > 2 && $timeDiff < 60) {
        $timeDiff = floor(abs($timeDiff)) . "m";
    } elseif ($timeDiff > 60 && $timeDiff < 120) {
        $timeDiff = floor(abs($timeDiff / 60)) . "h";
    } elseif ($timeDiff < 1440) {
        $timeDiff = floor(abs($timeDiff / 60)) . "h";
    } elseif ($timeDiff > 1440 && $timeDiff < 2880) {
        $timeDiff = floor(abs($timeDiff / 1440)) . "d";
    } elseif ($timeDiff > 2880) {
        $timeDiff = floor(abs($timeDiff / 1440)) . "d";
    }

    return $timeDiff;
}

function dateDiffInDays($date1, $date2)
{
    // Calculating the difference in timestamps
    $diff = strtotime($date2) - strtotime($date1);

    // 1 day = 24 hours
    // 24 * 60 * 60 = 86400 seconds
    return abs(round($diff / 86400));
}
