<?php
function buildTree( $array, $parentId = 0) {
    // print_r($array); exit;
     $branch = array();
     foreach ($array as $element) {
         if ($element['parent_id'] == $parentId) {
             $children = buildTree($array, $element['id']);
             if ($children) {
                 $element['children'] = $children;
             }
             $branch[] = $element;
         }
     }
     return $branch;
}
function generate_timezone_list() 
{
	$zones_array = array();
	$timestamp = time();
	foreach(timezone_identifiers_list() as $key => $zone) {
	  date_default_timezone_set($zone);
	  $zones_array[$key]['zone'] = $zone;
	  $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
      
      $dateTime = new DateTime();
      $dateTime->setTimeZone(new DateTimeZone($zone));
      $zones_array[$key]['abb'] = $dateTime->format('T');
      
    }
	return $zones_array;
}
function random_number($id,$lenght) 
{
    $id_length = strlen($id);
    $num_lenght = $lenght - $id_length;
    $random_Number = substr(str_shuffle("0123456789"), 0, $num_lenght);
    return $id.$random_Number;
}

function time_ago($given_date)
{
    $match_date = strtotime($given_date);
    $date = strtotime(date("Y-m-d H:i:s"));
    $difference = $date - $match_date;
    if($difference < 86400)
        return "Today,".date("H:i:s A",$match_date);
    else if ($difference < 172800)
        return "Yesterday,".date("H:i:s A",$match_date);
    else
        return date("Y-m-d,H:i:s A",$match_date);
}
function number_format_short( $n, $precision = 1 ) {
    if ($n < 900) {
        // 0 - 900
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        // 0.9k-850k
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        // 0.9m-850m
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        // 0.9b-850b
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        // 0.9t+
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }
  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
  // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if ( $precision > 0 ) {
        $dotzero = '.' . str_repeat( '0', $precision );
        $n_format = str_replace( $dotzero, '', $n_format );
    }
    return $n_format . $suffix;
}
function human_number($number) {
    $number = preg_replace('/[^\d]+/', '', $number);
    if (!is_numeric($number)) {
        return 0;
    }
    if ($number < 1000) {
        return $number;
    }
    $unit = intval(log($number, 1000));
    $units = ['', 'K', 'M', 'B', 'T', 'Q'];
    if (array_key_exists($unit, $units)) {
        return sprintf('%s%s', rtrim(number_format($number / pow(1000, $unit), 1), '.0'), $units[$unit]);
    }
    return $number;
}
function getIp()
{
	$ip = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
	return $ip;
}
function image_url($folder,$file_name)
{   
	$file_name = trim($file_name)=='' || !file_exists(storage_path(DefaultStorage.'/'.$folder.'/'.$file_name)) ? 'noimage.png' : $file_name;
	return url($folder.'/'.$file_name);
}

function image_path($folder,$file_name)
{
	$file_name = trim($file_name)=='' || !file_exists(storage_path(DefaultStorage.'/'.$folder.'/'.$file_name)) ? 'noimage.png' : $file_name;
	return storage_path(DefaultStorage.'/'.$folder.'/'.$file_name);
}

function base_url($var="")
{
	return url($var);
}

function root_path($var="")
{
	return base_path($var);
}

function cdn_url($var="")
{
	return url($var);
}
function  secondsToTime($inputSeconds) {

    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;

    // extract days
    $days = floor($inputSeconds / $secondsInADay);

    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    // extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    // return the final array
    $obj = array(
        'd' => (int) $days,
        'h' => (int) $hours,
        'm' => (int) $minutes,
        's' => (int) $seconds,
    );
    return $obj;
}
function is_valid_youtube_link($url="")
{
    $regex_pattern = "/(youtube.com|youtu.be)\/(watch)?(\?v=)?(\S+)?/";

    if(!preg_match($regex_pattern, $url, $match)){      
        return false;
    } else {
        if(empty($match[4]))
            return false;
        else
        {
            $hit_link = json_decode(file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=id&id={$match[4]}&key=AIzaSyDtSNukiq0KEHTzyo3XYdzeaWQnGiS3Rww"),1);            
            if(empty($hit_link['items']))
                return false;
        }
    } 

    return true;
}
function SendGridMail($to,$subject,$body)
{   
    $url = 'https://api.sendgrid.com/';
    $json_string = ['to' => [$to, $to],'category' => 'website'];
    $params = array(
        'api_user'  => "tollpaysapp",
        'api_key'   => "Tollpays.com2017",
        'x-smtpapi' => json_encode($json_string),
        'to'        => $to,
        'subject'   => $subject,
        'html'      => $body,
        'text'      => $body,
        'from'      => "no-reply@tollpays.com",
      );
    $request =  $url.'api/mail.send.json';
    $response =  CURL($request,$params);
    
    return true;
}
function rdd($arr)
{
	echo '<textarea style="width:100%;height:800px" width="100%" readonly height="800">';
	print_r($arr);
	echo '</textarea>';	
}
