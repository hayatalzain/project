<?php
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\Topics;


/*function t($phrase)
{
    $uword = explode('.',$phrase)[1];
    $sword = strtolower($phrase);
    if (Lang::has($sword))
    return __($sword);
    return ucwords($uword);
} */

function t($key,$placeholder=[],$locale=null)
{

    $group = 'admin';
    if(is_null($locale))
    $locale = config('app.locale');
    $key = trim($key);
    $word = $group.'.'.$key;
    if (Lang::has($word))
        return trans($word,$placeholder,$locale);

    $messages = [
        $word => $key,
    ];

    app('translator')->addLines($messages, $locale);
    $langs = ['ar','en'];
    foreach ($langs as $lang) {
    $translation_file = base_path() . '/resources/lang/'.$lang.'/' . $group . '.php';
    $fh = fopen($translation_file, 'r+');
    $new_key = "    '$key' => '$key',\n];\n";
    fseek($fh, -4, SEEK_END);
    fwrite($fh, $new_key);
    fclose($fh);
     }
    return trans($word,$placeholder,$locale);
    return $key;

}

function isRtl() {
    return app()->getLocale() === 'ar';
}

function direction() {
    return isRtl()? 'rtl' : 'ltr';
}


function currentLanguage() {
    return app()->getLocale();
}







function MimeFile($extension)
{
    /*
     Video Type     Extension       MIME Type
    Flash           .flv            video/x-flv
    MPEG-4          .mp4            video/mp4
    iPhone Index    .m3u8           application/x-mpegURL
    iPhone Segment  .ts             video/MP2T
    3GP Mobile      .3gp            video/3gpp
    QuickTime       .mov            video/quicktime
    A/V Interleave  .avi            video/x-msvideo
    Windows Media   .wmv            video/x-ms-wmv
    */
  $ext_photos = ['png','jpg','jpeg','gif'];
    return in_array($extension,$ext_photos) ? 'photo' : 'video';

}



function split_string($string,$count=2){

//Using the explode method
$arr_ph = explode(" ",$string,$count);

if(!isset($arr_ph[1]))
  $arr_ph[1] = '';
  return $arr_ph;

}

function check_mobile($mobile){

 if(starts_with($mobile, '05')){
   return '+966'.substr($mobile,1,9);
 }
  if(starts_with($mobile, '03')){
   return '+966'.substr($mobile,1,9);
 }
  if(starts_with($mobile, '00966')){
   return '+'.substr($mobile,2,12);
 }
   if(starts_with($mobile, '966')){
   return '+'.$mobile;
 }

 return $mobile;


 //   $mobile = str_replace('05', '+9665', $mobile);

}







/*
 |--------------------------------------------------------------------------
 | Send sms
 |--------------------------------------------------------------------------
 |
 */
function send_sms($numbers,$msg,$date='',$time='')
{

  //https://github.com/IsmailShurrab/Mobilyws-Laraval
    $numbers = str_replace('+','',$numbers);

    return  Mobily::send($numbers, $msg,$date);


    $sms_settings = \App\Models\Setting::firstOrFail();

    $settingsSmsGateway = $sms_settings->gateway;
    $settingsSmsUsername = urlencode($sms_settings->username);
    $settingsSmsPassword = urlencode($sms_settings->password);
    $settingsSmsSender = urlencode($sms_settings->sender);

    $msg = urlencode($msg);

    if (is_array($numbers)) {
        $numbers = implode(',', $numbers);
    }

    if(strpos($settingsSmsGateway, 'dreams')) {
        $url = $settingsSmsGateway . "?" . "username=" . $settingsSmsUsername . "&password=" . $settingsSmsPassword . "&numbers=" . $numbers . "&sender=" . $settingsSmsSender . "&message=" . $msg . "&lang=ar";
    } else {
        $url = $settingsSmsGateway . "?" .   "mobile=" . $settingsSmsUsername . "&password=" . $settingsSmsPassword . "&numbers=" . $numbers . "&sender=" . $settingsSmsSender . "&msg="     . $msg . "&lang=3";
    }

    \Log::info('real '. $url);
    if (config('app.debug')) {
        \Log::info('debug '. $url);
        return 100;
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url,
    ));
    $result = curl_exec($curl);
    curl_close($curl);

    return $result;
}




 function nearest($lat,$lng,$radius=1){

    // Km
    $angle_radius = $radius / 111;
    $location['min_lat'] = $lat - $angle_radius;
    $location['max_lat'] = $lat + $angle_radius;
    $location['min_lng'] = $lng - $angle_radius;
    $location['max_lng'] = $lng + $angle_radius;

    return (object)$location;

}










/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/*::                                                                         :*/
/*::  This routine calculates the distance between two points (given the     :*/
/*::  latitude/longitude of those points). It is being used to calculate     :*/
/*::  the distance between two locations using GeoDataSource(TM) Products    :*/
/*::                                                                         :*/
/*::  Definitions:                                                           :*/
/*::    South latitudes are negative, east longitudes are positive           :*/
/*::                                                                         :*/
/*::  Passed to function:                                                    :*/
/*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
/*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
/*::    unit = the unit you desire for results                               :*/
/*::           where: 'M' is statute miles (default)                         :*/
/*::                  'K' is kilometers                                      :*/
/*::                  'N' is nautical miles                                  :*/
/*::  Worldwide cities and other features databases with latitude longitude  :*/
/*::  are available at https://www.geodatasource.com                          :*/
/*::                                                                         :*/
/*::  For enquiries, please contact sales@geodatasource.com                  :*/
/*::                                                                         :*/
/*::  Official Web site: https://www.geodatasource.com                        :*/
/*::                                                                         :*/
/*::         GeoDataSource.com (C) All Rights Reserved 2017                  :*/
/*::                                                                         :*/
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}

//echo distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
//echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
//echo distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";











/**
 * Calculates the great-circle distance between two points, with
 * the Vincenty formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
 function DistanceFromLatLonInKm(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $lonDelta = $lonTo - $lonFrom;
  $a = pow(cos($latTo) * sin($lonDelta), 2) +
    pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
  $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

  $angle = atan2(sqrt($a), $b);
  return $angle * $earthRadius;
}










function assets($path='',$relative=false)
{
    return $relative ?  'public/'.$path : url('public/'.$path);
}



function slug($string)
{
    return preg_replace('/\s+/u', '-', trim($string));
}



function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }





function generateInvoiceNumber($model) {


 $year = date('Y');
 $expNum = 0;
//get last record
$record = $model::latest()->first();
 if($record)
 list($year,$expNum) = explode('-', $record->invoice_id);

//check first day in a year
if ( date('z') === '0' ) {
    $nextInvoiceNumber = date('Y').'-0001';
} else {
    //increase 1 with last invoice number
    $nextInvoiceNumber = $year.'-'. ((int)$expNum+1);
}

 return $nextInvoiceNumber;
//now add into database $nextInvoiceNumber as a next number.
}


function defaultImage()
{
    return "public/assets/img/default.png";
}



function status($status, $type = '')
{
    $color = [
        '0' => 'danger',
        '1' => 'success',
        'pending' => 'warning',
        'active' => 'success',
        'accepted' => 'success',
        'delayed' => 'default',
        'rejected' => 'danger',
        'cancelled' => 'default',
        'inactive' => 'danger',
        'waiting' => 'warning',
        'acceptable' => 'info',
        'unacceptable' => 'danger',
        'winners' => 'success',
        'done' => 'success',
        'pass' => 'info',
        'shipping' => 'warning',
        'new' => 'warning',
        'completed' => 'success',

    ];

    $text = [
        '0' => t('admin.Inactive'),
        '1' => t('admin.Active'),
        'pending' => t('admin.Pending'),
        'active' => t('admin.Active'),
        'accepted' => 'مقبول',
        'delayed' => 'مؤجل',
        'rejected' => 'مرفوض',
        'cancelled' => t('admin.Cancelled'),
        'inactive' => t('admin.inactive'),
        'waiting' => t('admin.waiting'),
        'acceptable' => 'مقبول',
        'unacceptable' => 'مرفوض',
        'winners' => 'فائز',
        'done' => t('admin.Done'),
        'pass' => 'تم تمريرها',
        'shipping' => t('admin.Shipping'),
        'new' => t('admin.New'),
        'completed' => t('admin.completed'),
    ];

    if ($type == 't')
        return $text[$status];

    if ($type == 'c')
        return $color[$status];


    return "<label class='label label-mini label-{$color[$status]}'>{$text[$status]}<label>";
}


function pic($src, $class = 'full')
{

    $html = "<img class='  " . $class . "' src='" . asset($src) . "'>";

    return $html;

}

function ext($filename, $style = false)
{

    //$ext = File::extension($filename);

    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    if (!$style)
        return $ext;
    return $html = "<img class='' src='" . asset('public/assets/img/ext/' . $ext . '.png') . "'>";
}



function IsLang($lang='ar')
{
    return session('lang') == $lang;
}

function CurrentLang()
{
    return session('lang','en');
}


 function rating($val,$max=5){
     $html = '';
        for ($i = 1; $i <=$max; $i++) {

            if ($i <= $val)
                $html .= "<span><i class='fa fa-star fa-lg active'></i></span>";
            else
                $html .= "<span><i class='fa fa-star-o fa-lg '></i></span>";

        }
        return $html;

  }

function isAPI(){
return request()->is('api/*');
}


function versions()
{
    return ['v1'];
}


function base64ToFile($data){

$file_name = 'attach_'.time().'.'.getExtBase64($data);
$path = 'uploads/user_attachments/'.$file_name;
$uploadPath  = public_path($path);
if(!file_put_contents($uploadPath , base64_decode($data)));
 $path = '';
return $path;

}

function getExtBase64($data){

$pos  = strpos($data, ';');
$mimi = explode(':', substr($data, 0, $pos))[1];
return $ext = explode('/', $mimi)[1];
}


function paginate($object)
{
    return [
        'current_page' => $object->currentPage(),
        'items' => $object->items(),
        'first_page_url' => $object->url(1),
        'from' => $object->firstItem(),
        'last_page' => $object->lastPage(),
        'last_page_url' => $object->url($object->lastPage()),
        'next_page_url' => $object->nextPageUrl(),
        'per_page' => $object->perPage(),
        'prev_page_url' => $object->previousPageUrl(),
        'to' => $object->lastItem(),
        'total' => $object->total(),
    ];
}


function paginate_message($object)
{

    $items = [];
    foreach($object->items() as $key => $item) {
    foreach($item['data'] as $k => $val) {
    $items[$key][$k] = $val;

    // $items[$key] = ['id' => $item->id,'title' => $item->data['title'],'body' => $item->data['body'],'created_at' => $item->created_at ];
    /* if(isset($item->data['title']))
      $items[$key]['title'] = $item->data['title']; */
    }
    $items[$key]['notification_id'] = $item->id;
    $items[$key]['created_at'] = $item->created_at->format('Y-m-d H:i:s');
    }

    return [
        'current_page' => $object->currentPage(),
        'items' => $items,
        'first_page_url' => $object->url(1),
        'from' => $object->firstItem(),
        'last_page' => $object->lastPage(),
        'last_page_url' => $object->url($object->lastPage()),
        'next_page_url' => $object->nextPageUrl(),
        'per_page' => $object->perPage(),
        'prev_page_url' => $object->previousPageUrl(),
        'to' => $object->lastItem(),
        'total' => $object->total(),
    ];
}





function send_push($fcm_token, $payload_data = [])
{
    error_log('fcm_token: ' . json_encode($fcm_token));
    if ($fcm_token instanceof Illuminate\Support\Collection) {
        $fcm_token = $fcm_token->toArray();
    }

    if (!$fcm_token) {
        return true;
    }

    if (is_string($fcm_token)) {
        $fcm_token = [$fcm_token];
    }

    $fcm_token = array_filter($fcm_token);

    if (!$fcm_token) {
        return true;
    }

    $notificationBuilder = new PayloadNotificationBuilder($payload_data['title']);
    $notificationBuilder->setBody($payload_data['body'])
        ->setSound('default');
    $notification = $notificationBuilder->build();

    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData($payload_data);
    $data = $dataBuilder->build();

    FCM::sendTo($fcm_token, null, $notification, $data);

}


function send_push_to_topic($topic_name,$payload_data)
{

    $notificationBuilder = new PayloadNotificationBuilder($payload_data['title']);
    $notificationBuilder->setBody($payload_data['body'])
        ->setSound('default');
    $notification = $notificationBuilder->build();

    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData($payload_data);

    $data = $dataBuilder->build();

    $topic = new Topics();
    $topic->topic($topic_name);
    \Log::info($topic_name);
    FCM::sendToTopic($topic, null, $notification, $data);

}



function getOnly($only, $array)
{
    $data = [];
    foreach ($only as $id) {
        if (isset($array[$id])) {
            $data[$id] = $array[$id];
        }
    }
    return $data;
}



function status_text($status){

 $title = ['pending' => 'المعلقة','accepted' => 'المقبولة','cancelled' => 'الملغية','rejected' => 'المرفوضة'];

 return $title[$status];

}




function cached($index = 'settings',$col=false)
{

    //Cache::forget('cities');
    $cache['settings'] = Cache::remember('settings', 60 * 48, function () {
        return \App\Models\Setting::first();
    });

    if (!isset($cache[$index]))
        return $index;
    if(!$col)
    return $cache[$index];
    return $cache[$index]->{$col};

}


function options($col=false,$attr=false)
{
     $options = Cache::remember('options', 60 * 48, function() {
       $options = \App\Models\Setting::get();
       $new_options = [];
      foreach ($options as $key => $option) {
        $new_options[$option->key] = $option->value;
       }
       return (object)$new_options;
    });
     if($attr)
    return $options->{$col}[$attr]?? 'not set '.$col;
    return !$col ? $options : $options->{$col};

}


function destroyFile($file){

 if(!empty($file) and File::exists(public_path($file)))
  File::delete(public_path($file));

}





function curl_get_contents($url)
{
    $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
   $html = curl_exec($ch);
   $data = curl_exec($ch);
   curl_close($ch);
   return $data;
}




 function get_page_info($url){


    $urlInfo= [];

    $str = curl_get_contents($url);
   // $str = file_get_contents($url);
    $tags = get_meta_tags($url);
   // dd($tags);
   // dd($str);
    if(strlen($str)>0){

            $str = trim(preg_replace('/\s+/', ' ', $str));
            preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title);

            $urlInfo['title'] = $title[1];
          //  $urlInfo['description']=$tags['description'];
          //  $urlInfo['keywords']=$tags['keywords'];
            preg_match('/og:image"\s*content="([^"]+)"/i', $str, $photo);
           // if($photo)
            $urlInfo['photo'] = $photo;


            preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $str, $images);


            if(count($images)>1)
            $urlInfo['photos'] = $images;
            else
            $urlInfo['logo']=null;

            foreach ($tags as $key => $tag) {
             if(str_contains($key, 'image') || str_contains($key, 'photo')){
             $urlInfo['photo'] = $tag;
             if(filter_var($tag, FILTER_VALIDATE_URL))
               break;
                 }
               }



            $urlInfo['url'] = $url;

            return $urlInfo;
    }
  }








function file_get_contents_curl($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    $html = curl_exec($ch);
    curl_close($ch);

//parsing begins here:
$doc = new \DOMDocument();
@$doc->loadHTML($html);
$nodes = $doc->getElementsByTagName('title');

//get and display what you need:
//$tags['title'][] = $title = $nodes->item(0)->nodeValue;

$metas = $doc->getElementsByTagName('meta');

for ($i = 0; $i < $metas->length; $i++)
{
      $meta = $metas->item($i);
    $tags[$meta->getAttribute('name')][] =  $meta->getAttribute('content');
    if($meta->getAttribute('name') == 'description')
        $description = $meta->getAttribute('content');
    if($meta->getAttribute('name') == 'keywords')
        $keywords = $meta->getAttribute('content');
}
 return $tags;
echo "Title: $title". '<br/><br/>';
echo "Description: $description". '<br/><br/>';
echo "Keywords: $keywords";

}





function xpath($url,$query=''){


    $doc = new \DOMDocument();
  //  @$doc->loadHTML($html);
    libxml_use_internal_errors(true);

$context = stream_context_create(
    array(
        "http" => array(
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
        )
    )
);

   $response = file_get_contents($url, false, $context);


  //  $response = file_get_contents($url);

  // dd($response);
   // $doc->loadHTMLFile($url);
    $doc->loadHTML($response);
    // or @$doc->loadHTMLFile($URLVF['url']);

    $data = $doc->getElementsByTagName("title");
     // return [$data[0]->nodeValue]; //."\n";
    // return [$data->nodeValue]; //."\n";
    // return [$data->nodeName]; //."\n";

   //meta[@name='description']/@content


    $xpath = new \DOMXPath($doc);
    $photo =  $xpath->query("/html/head/meta[@property='og:image']/@content");
    $photo2 =  $xpath->query("/html/head/meta[@property='image']/@content");
    $photo3 =  $xpath->query("//meta[@name='Image']/@content");

    if(isset($photo[0]))
    $info['photo'] = $photo[0]->nodeValue;
    elseif(isset($photo2[0]))
    $info['photo'] = $photo2[0]->nodeValue;
    elseif(isset($photo3[0]))
    $info['photo'] = $photo3[0]->nodeValue;
    // utf8_decode
    $info['title'] =  (trim(str_replace('"','',$xpath->query("//title")[0]->nodeValue)));

    $metaContentAttributeNodes = $xpath->query("/html/head");
    foreach($metaContentAttributeNodes as $node) {

        $attr = $node->nodeName.' _ ';
        $attr = $node->getAttribute('property')."_";
        $node->nodeValue . "<br/>";
    }

    return $info;

}




function diffdays($date1,$date2=null){

// $date2 = $date2? : time();
// $your_date = strtotime($date1);
// $datediff = $your_date - $date2;

// return round($datediff / (60 * 60 * 24));


$date1=date_create($date1);
$date2=date_create(date('Y-m-d'));
$diff=date_diff($date2,$date1);
$days = $diff->format("%a");
if($days==0)
 return 'اليوم';
return $diff->format("%a يوم");
return $diff->format("%R%a days");


}


function arabic_date($datetime){

 $months = ["Jan" => "يناير", "Feb" => "فبراير", "Mar" => "مارس", "Apr" => "أبريل", "May" => "مايو", "Jun" => "يونيو", "Jul" => "يوليو", "Aug" => "أغسطس", "Sep" => "سبتمبر", "Oct" => "أكتوبر", "Nov" => "نوفمبر", "Dec" => "ديسمبر"];
 $days = ["Sat" => "السبت", "Sun" => "الأحد", "Mon" => "الإثنين", "Tue" => "الثلاثاء", "Wed" => "الأربعاء", "Thu" => "الخميس", "Fri" => "الجمعة"];
 $am_pm = ['AM' => 'صباحاً', 'PM' => 'مساءً'];

 $_month = $months[date('M',strtotime($datetime))];
 $_day    = $days[date('D',strtotime($datetime))];
 $_day    = date('d',strtotime($datetime));
 $_time    = date('h:i',strtotime($datetime));
 $_am_pm  = $am_pm[date('A',strtotime($datetime))];

 return '('.$_day.' '.$_month.' - '. $_time .' '.$_am_pm.')';

}



function delete_collection($documents, $batchSize=5)
{
   // $documents = $collectionReference->documents();
    while (!$documents->isEmpty()) {
        foreach ($documents as $document) {
            printf('Deleting document %s' . PHP_EOL, $document->id());
            $document->reference()->delete();
        }
      //  $documents = $collectionReference->limit($batchSize)->documents();
    }
}









function GetRating($stars=0){

  $stars = intval($stars);
  $stars_arr = [t('Zero Star'),t('One Star'),t('Two Star'),t('Three Star'),t('Four Star'),t('Five Star')];

  return $stars_arr[$stars];
 $rating = "";
 for($i=1; $i<=5; $i++){
    if($i<= $stars)
   $rating .= " <span class='fa fa-star checked'></span>";
   else
   $rating .= " <span class='fa fa-star'></span>";
 }

 return $rating;

}

