<?php
echo phpinfo();
ini_set('display_errors', false);
ini_set('max_execution_time', 600);

const POST_ID = '1689393634653033';
const ACCESS_TOKEN = 'EAACEdEose0cBAPA9Qi8SkyZCOTkEUCjZCiIvjeL9pIZCSSEIIvWrEoGipyEN0Fzken0dIXOBCFMxHQt0AmFX5zrxybV8HJ3LEahveTSGZANwfxUETzl6uaVtN0ZBeYPOMud3oRyGD1iHwy4BMdIzB4l0fVWP3LnfbJWSlfmoDbwZDZD';
const PARAMS = '&limit=1000';
const PHONE_NUMBER_LENGTH = 10;
const FILE_PATH = 'D:/FB_Ads/';


if ($_GET['callback']) {
    header('Content-type: application/x-javascript');
}else{
    header('content-type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
}

$function = $_GET['function'];
if (function_exists($function)) {
    $function();
} else {
    echo json_encode(array("status" => "error", "msg" => 'function not existed'));
    exit();
}
//http://fb-get-phonenumber.local:8080/?function=getPhoneNumber&audience_name=TamTrangSuaNon&post_id=1689393634653033&limit=1000&token=EAACEdEose0cBAFvlcXrHkckk5Rd5169eLJvYYOpq2zU61ZAfBU1QWPNhYtR1exNN1O2HI0Ib6sXbytajxVOSvlDBFRK3LP7e5qKiSxmdXu6sDL96Ffc117eaffchGYjGlXl85u9C2MIUaUYww9f7qLUmfSDphKkf5XVpawAZDZD
function getPhoneNumber(){
    $audience_name = $_GET['audience_name'];
    $access_token = $_GET['token'];
    $limit = $_GET['limit'];
    $post_id = $_GET['post_id'];
    if(!$access_token) $access_token = ACCESS_TOKEN;
    $filename = $audience_name . '_' . time() . '_' . date('d_m_Y');
    $phone_number_allow = array('090','091','092','093','094','095','096','097','098','099');
    $ch = curl_init();
    $api_url = 'https://graph.facebook.com/' . $post_id . '/comments?access_token=' . $access_token . '&limit=' . $limit;
//    echo $api_url;die();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $output = curl_exec($ch);
    $result = json_decode($output);
    $data = $result->data;
    if($data){
        $myfile = openFile($filename);
        foreach($data as $comment){
            $message = $comment->message;
            foreach($phone_number_allow as $num){
                $find = strpos($message, $num, 0);
                if($find !== false) {
                    $phone_number = substr($message, $find, PHONE_NUMBER_LENGTH);
                    if(!empty($phone_number)){
                        $record = str_replace(" ", "", $phone_number);
                        $record = str_replace(".", "", $record);
                        if(strlen($record) == PHONE_NUMBER_LENGTH){
                            $item = $record."\n";
                            writeFile($myfile, $item);
                        }
                    }
                }
            }
        }
        closeFile($filename);
    } else {
        print_r($result->error->message);
        exit();
    }
    echo "Get Data successfully.";
    exit();

    curl_close($ch);
}
function openFile($filename){
    $myfile = fopen(FILE_PATH . $filename, "w") or die("Unable to open file!");
    return $myfile;
}
function writeFile($myfile, $data){
    fwrite($myfile, $data);
}
function closeFile($filename){
    fclose($filename);
}





