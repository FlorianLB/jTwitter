<?php

/**
 * @package jTwitter
 * @author Florian Lonqueu-Brochard
 *
 **/
class jTwitter{
     
     public static $default_count = '3';
     public static $expire_ago = '1 hour';
     
     public static $cache_dir = 'uploads/jTwitter/' ; //relative to www path
     public static $cache_dir_name;
     
     private static $initialized = false;
     
     
     protected $user;
     protected $count;
     protected $doParsing;
     
     protected function __construct ($user, $count, $doParsing) {
          $this->user = $user;
          $this->doParsing = $doParsing;
          
          if ($count && is_numeric($count))
               $this->count = $count;
          else
               $this->count = self::$default_count;
     }
     
     protected static function init(){
          if (!self::$initialized) {
               
               self::$cache_dir_name = $GLOBALS['gJConfig']->urlengine['basePath'].self::$cache_dir;
               self::$cache_dir = JELIX_APP_WWW_PATH.self::$cache_dir;
               
               self::$initialized = true;
          }
     }
     
     
     protected function buildTimelinePath () {
          return 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name='.$this->user.'&count='.$this->count;
     }
     
     
     protected function getJson () {

          $ch = curl_init($this->buildTimelinePath() );
          curl_setopt($ch, CURLOPT_TIMEOUT, 30);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
      
          return curl_exec($ch);
     }
     
     protected static function _getFormattedDate($created_at){
        
        $created_at = strtotime($created_at);
        $date = null;
        
        //Hours
        $hours = self::_ago($created_at);
        if ($hours < 1)
            $date = jLocale::get('twitter.date.lesshour');
        elseif ($hours == 1)
            $date = jLocale::get('twitter.date.prefix').' '.$hours.' '.jLocale::get('twitter.date.suffix.hour');
        else{
            if ($hours < 24)
                $date .= jLocale::get('twitter.date.prefix').' '.$hours.' '.jLocale::get('twitter.date.suffix.hours');
            else {
                $days = self::_ago($created_at, 'day');
                
                if ($days <= 1)
                    $date = jLocale::get('twitter.date.prefix').' '.$days.' '.jLocale::get('twitter.date.suffix.day');
                else
                    $date = jLocale::get('twitter.date.prefix').' '.$days.' '.jLocale::get('twitter.date.suffix.days');
            }
            
        }
        
        return $date;
    }
    
    private static function _ago($time1, $unit = 'hour'){
          $time2 = time();
          if ($time1 > $time2)
              $time = $time1 - $time2;
          else
              $time = $time2 - $time1;
       
          if ($unit == 'day')
               $granularity = 3600*24;
          else
               $granularity = 3600;
          $time = $time / $granularity;
          return round($time);
    }

     protected function extractData($json){
          $data = json_decode($json);
          
          $timeline = array();
          foreach($data as $item){
               $date = self::_getFormattedDate($item->created_at);
               
               $text = $this->doParsing ? self::parseText($item->text) : $item->text;
               
               array_push( $timeline, array('text' => $text, 'date' => $date) );
          }
          
          return $timeline;
     }
     
     protected static function parseText($text){
          
          $replace = array(
                         '/(http:\/\/[^ ]*)/i' => '<a href="$1">$1</a>',
                         '/@([a-z]*)/i' => '@<a href="http://twitter.com/$1">$1</a>' ,
                         '/#([a-zA-Z]*)/i' => '<a href="http://twitter.com/#search/%23$1">#$1</a>'
                    );
          
          return preg_replace(array_keys($replace), array_values($replace), $text);
          
          //return preg_replace('/(http:\/\/[^ ]*)/i', '<a href="$1">$1</a>', $text);
          
          //return $text;
     }
     
  protected static function isCacheValid ($file_path) {
    if (file_exists($file_path))
    {
      if (filectime($file_path) < strtotime("+".self::$expire_ago))
      {
        // file exists and cache is valid
        return true;
      }
      else
      {
        // file exists but cache has expired
        unlink($file_path);
      }
    }

    // no file
    return false;
  }
     
     public static function getTimeline ($user, $count = null, $doParsing = true) {
          
          self::init();
          $hash = md5($user.'-'.$count.'-'.$doParsing);
          $cacheFile = self::$cache_dir.$hash.'.txt';
          
          if (file_exists($cacheFile) && self::isCacheValid($cacheFile)){       
               return unserialize( jFile::read($cacheFile) );
          }
          else {
               $instance = new self($user, $count, $doParsing);
               
               $timeline= $instance->extractData( $instance->getJson() );   
               
               jFile::write( $cacheFile, serialize($timeline) );
               
               return $timeline;
          }
         
     }

/*
     //Mock
     $timelineOriginal[] = (object)(array('text' => '5 minutes avant ', 'created_at' => date('r', time()-5*60)));
     $timelineOriginal[] = (object)(array('text' => '4heures avant ', 'created_at' => date('r', time()-60*60*4)));
     $timelineOriginal[] = (object)(array('text' => '16heures avant ', 'created_at' => date('r', time()-60*60*16)));
     $timelineOriginal[] = (object)(array('text' => '25heures avant ', 'created_at' => date('r', time()-60*60*25)));
     $timelineOriginal[] = (object)(array('text' => '45heures avant ', 'created_at' => date('r', time()-60*60*45)));
*/

}