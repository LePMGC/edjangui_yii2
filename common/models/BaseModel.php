<?php

namespace common\models;

use Yii;

/**
 *
 */
class BaseModel extends \yii\db\ActiveRecord
{
	/**
	* @param string $attribute to validate
	* @param string $param
	* @return boolean
	*/
	public function isAValidPhoneNumber($attribute, $params){
        if( (strlen($this->$attribute)!=9) || (!preg_match("/^[6,0][0-9]{8}/", $this->$attribute) && !preg_match("/^22|33[0-9]{7}/", $this->$attribute))) {
            $this->addError($attribute, \Yii::t('app', 'This is not a valid phone number'));
            return false;
        }
        return true;
    }

    /**
    * Edit the model before save
    * @param  object
    * @return boolean if model is allowed to be saved
    */
    public function beforeSave($insert){
        if (parent::beforeSave($insert)) {        	
            if ($this->isNewRecord) {
                if((strcmp(Yii::$app->id, 'app-console')!=0) && !empty(\Yii::$app->getUser()->getId()))
                    $userID = \Yii::$app->getUser()->getId();
                else
                    $userID = 1;

                if(empty($this->created_by))
                    $this->created_by = $userID;

                if(empty($this->updated_by))
                    $this->updated_by = $userID;
                
                $this->created_on = Date("Y-m-d H:i:s");                
                $this->updated_on = Date("Y-m-d H:i:s");
            }else{
                if((strcmp(Yii::$app->id, 'app-console')!=0) && !empty(\Yii::$app->getUser()->getId()))                    
            	   $this->updated_by = \Yii::$app->getUser()->getId();
                $this->updated_on = Date("Y-m-d H:i:s");
            }
        	return true;
        }
        return false;
    }

    /**
    * Return the month name of the given date
    * @param string
    * @return string
    */
    public function getMonthNameFromDate($input_date){
        $month = intval(substr($input_date,5,2));
        $monthNames = explode(" ", \Yii::t('app', "Pmgc Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec"));
        return $monthNames[$month];
    }

    /**
    * Return the formatted date from given mysql date
    * @param string
    * @return string
    */
    public function getFormattedDate($date, $format){
        if(strcmp($format, "FR")==0){
            return substr($date,8,2).".".$this->getMonthNameFromDate($date).".".substr($date,0,4);
        }
    }

    /**
    * Return the mysql date format of the given french date
    * @param string $date
    * @return string
    */
    public static function getMysQLFormattedDateFromFrenchDate($date){
        $frenchToEnglishMonths = array(
            'janv.' => 'Jan',
            'févr.' => 'Feb',
            'mars' => 'Mar',
            'avr.' => 'Apr',
            'mai' => 'May',
            'juin' => 'Jun',
            'juil.' => 'Jul',
            'août' => 'Aug',
            'sept.' => 'Sep',
            'oct.' => 'Oct',
            'nov.' => 'Nov',
            'déc.' => 'Dec',
        );

        $dateElements = explode(" ", $date);
        $result =  $dateElements[0]." ".$frenchToEnglishMonths[$dateElements[1]]." ".$dateElements[2];
        return date('Y-m-d', strtotime($result));
    }

    /**
    * Return the distance between 2 couple of coordinates
    * @param  float, float, float, float, string
    * @return float
    */
    public function distance($lat1, $lng1, $lat2, $lng2, $unit) {

        $earthRadius = 3958.75;

        $dLat = deg2rad($lat2-$lat1);
        $dLng = deg2rad($lng2-$lng1);


        $a = sin($dLat/2) * sin($dLat/2) +
           cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
           sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $dist = $earthRadius * $c;

        // from miles
        $meterConversion = 1609;
        $geopointDistance = $dist * $meterConversion;

        return $geopointDistance/1000;
    }

    /**
    * get all the months of the Year
    * @return array
    */
    public function getMonthsOfYear(){
        return [
            0 => Yii::t('app', 'January'),
            1 => Yii::t('app', 'February'),
            2 => Yii::t('app', 'March'),
            3 => Yii::t('app', 'April'),
            4 => Yii::t('app', 'May'),
            5 => Yii::t('app', 'June'),
            6 => Yii::t('app', 'July'),
            7 => Yii::t('app', 'August'),
            8 => Yii::t('app', 'September'),
            9 => Yii::t('app', 'October'),
            10 => Yii::t('app', 'November'),
            11 => Yii::t('app', 'December')
        ];
    }

    /**
    * get all the days of the week
    * @return array
    */
    public function getDaysOfWeek(){
        return [
            0 => Yii::t('app', 'Monday'),
            1 => Yii::t('app', 'Tuesday'),
            2 => Yii::t('app', 'Wednesday'),
            3 => Yii::t('app', 'Thursday'),
            4 => Yii::t('app', 'Friday'),
            5 => Yii::t('app', 'Saturday'),
            6 => Yii::t('app', 'Sunday')
        ];
    }

    /**
    * get all the days of the month
    * @return array
    */
    public function getDaysOfMonth(){
        return [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
            21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
            31
        ];
    }

    /**
    * get all the weeks of the year
    * @return array
    */
    public function getWeeksOfYear(){
        return [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
            21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
            31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
            41, 42, 43, 44, 45, 46, 47, 48, 49, 50,
            51, 52
        ];
    }

    /**
    * Check if the haystack string starts with needle
    */
    public static function startsWith($haystack, $needle){
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
    * Check if the haystack string ends with needle
    */
    public static function endsWith($haystack, $needle){
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }


    //Sort an array
    public static function array_sort($array, $type='asc'){
        $result=array();
        foreach($array as $var => $val){
            $set=false;
            foreach($result as $var2 => $val2){
                if($set==false){
                    if($val>$val2 && $type=='desc' || $val<$val2 && $type=='asc'){
                        $temp=array();
                        foreach($result as $var3 => $val3){
                            if($var3==$var2) $set=true;
                            if($set){
                                $temp[$var3]=$val3;
                                unset($result[$var3]);
                            }
                        }
                        $result[$var]=$val;   
                        foreach($temp as $var3 => $val3){
                            $result[$var3]=$val3;
                        }
                    }
                }
            }
            if(!$set){
                $result[$var]=$val;
            }
        }
        return $result;
    }

    /**
     * Sort a 2 dimensional array based on 1 or more indexes.
     * 
     * msort() can be used to sort a rowset like array on one or more
     * 'headers' (keys in the 2th array).
     * 
     * @param array        $array      The array to sort.
     * @param string|array $key        The index(es) to sort the array on.
     * @param int          $sort_flags The optional parameter to modify the sorting 
     *                                 behavior. This parameter does not work when 
     *                                 supplying an array in the $key parameter. 
     * 
     * @return array The sorted array.
     */
    public static function msort($array, $key, $sort_flags = SORT_REGULAR, $order = SORT_ASC) {
        if (is_array($array) && count($array) > 0) {
            if (!empty($key)) {
                $mapping = array();
                foreach ($array as $k => $v) {
                    $sort_key = '';
                    if (!is_array($key)) {
                        $sort_key = $v[$key];
                    } else {
                        // @TODO This should be fixed, now it will be sorted as string
                        foreach ($key as $key_key) {
                            $sort_key .= $v[$key_key];
                        }
                        //$sort_flags = SORT_STRING;
                    }
                    $mapping[$k] = $sort_key;
                }
                //asort($mapping, $sort_flags);
                switch ($order) {
                
                    case SORT_ASC:
                    asort($mapping, $sort_flags);
                    break;
                
                    case SORT_DESC:
                    arsort($mapping, $sort_flags);
                    break;
                }
                $sorted = array();
                foreach ($mapping as $k => $v) {
                    $sorted[] = $array[$k];
                }
                return $sorted;
            }
        }
        return $array;
    }

    /**
    * Get the list of days of a period of time defined by 2 dates
    * @param string $strDateFrom the given start date
    * @param string $strDateTo the given end date
    * @return array the list of days between those 2 dates
    */
    public static function getDateRangeArray($strDateFrom,$strDateTo){
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script

    $aryRange=array();

    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

    if ($iDateTo>=$iDateFrom)
    {
        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo)
        {
            $iDateFrom+=86400; // add 24 hours
            array_push($aryRange,date('Y-m-d',$iDateFrom));
        }
    }
    return $aryRange;
    }

    /**
    * Get the list of months of a period of time defined by 2 dates
    * @param string $strDateFrom the given start date
    * @param string $strDateTo the given end date
    * @return array the list of months between those 2 dates
    */
    public static function getMonthsRangeArray($strDateFrom,$strDateTo){
        $aryRange=array();

        $start    = new \DateTime($strDateFrom);
        $start->modify('first day of this month');
        $end      = new \DateTime($strDateTo);
        $end->modify('first day of next month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period   = new \DatePeriod($start, $interval, $end);

        $i = 0;
        foreach ($period as $dt) {
            $aryRange[$i++] = $dt->format("Y-n");
        }
        return $aryRange;
    }

    /**
    * Get the list of years of a period of time defined by 2 dates
    * @param string $strDateFrom the given start date
    * @param string $strDateTo the given end date
    * @return array the list of years between those 2 dates
    */
    public static function getYearsRangeArray($strDateFrom,$strDateTo){
        $aryRange=array();

        $start    = new \DateTime($strDateFrom);
        $start->modify('first day of this year');
        $end      = new \DateTime($strDateTo);
        $end->modify('first day of next year');
        $interval = \DateInterval::createFromDateString('1 year');
        $period   = new \DatePeriod($start, $interval, $end);

        $i = 0;
        foreach ($period as $dt) {
            $aryRange[$i++] = $dt->format("Y");
        }
        return $aryRange;
    }
    
    /**
    * Check if the current time is in the given period
    * @param string $t1 the start time of the period
    * @param string $t2 the end time of the period
    * @return boolean true if current is in the period, false if it is not.
    */
    public static function areWeInDataCollectionTime($t1, $t2) {
        $tn = Date("H:i:s");
        $t1 = +str_replace(":", "", $t1);
        $t2 = +str_replace(":", "", $t2);
        $tn = +str_replace(":", "", $tn);
        if ($t2 >= $t1) {
            return $t1 <= $tn && $tn < $t2;
        } else {
            return ! ($t2 <= $tn && $tn < $t1);
        }
    }

    public static function generatePIN($digits = 4){
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while($i < $digits){
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }

    public static function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds'){
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';
        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
        $password = str_shuffle($password);
        if(!$add_dashes)
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }


    /**
    */
    public function getAllMonthsBetween2Dates($startDate,$endDate){
        $start    = new \DateTime($startDate);
        $start->modify('first day of this month');
        $end      = new \DateTime($endDate);
        $end->modify('first day of next month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period   = new \DatePeriod($start, $interval, $end);

        $results = array();
        $i = 0;

        foreach ($period as $dt) {
            //echo $dt->format("Y-m") . "<br>\n";

            $results[$i++] = array(
                'name' => $dt->format("Y-M"),
                'start_date' => date('Y-m-01', strtotime($dt->format("Y-m-d"))),
                'end_date' => date('Y-m-t', strtotime($dt->format("Y-m-d")))
            );
        }

        return $results;
    }
}
