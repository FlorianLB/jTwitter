<?php
/**
* @package   test
* @subpackage jTwitter
* @author    yourname
* @copyright 2010 yourname
* @link      http://www.yourwebsite.undefined
* @license    All right reserved
*/

 jClasses::inc('jTwitter~jTwitter');

class timelineZone extends jZone {
    protected $_tplname='timeline';

    protected function _prepareTpl(){
        
        $user = $this->param('user');
        $count = $this->param('count', jTwitter::$default_count);
        $doParsing = $this->param('doParsing', true);
        
        $timeline = jTwitter::getTimeline($user, $count, $doParsing);
        
        $this->_tpl->assign('timeline', $timeline);
        $this->_tpl->assign('user', $user);
    }
    
    private static function _getFormattedDate($created_at){
        
        $created_at = strtotime($created_at);
        $date = null;
        
        //Hours
        $hours = self::_hoursAgo($created_at);
        if ($hours < 1)
            $date = jLocale::get('twitter.date.lesshour');
        elseif ($hours == 1)
            $date = jLocale::get('twitter.date.prefix').' '.$hours.' '.jLocale::get('twitter.date.suffix.hour');
        else{
            if ($hours < 24)
                $date .= jLocale::get('twitter.date.prefix').' '.$hours.' '.jLocale::get('twitter.date.suffix.hours');
            else {
                $days = self::_daysAgo($created_at);
                
                if ($days <= 1)
                    $date = jLocale::get('twitter.date.prefix').' '.$days.' '.jLocale::get('twitter.date.suffix.day');
                else
                    $date = jLocale::get('twitter.date.prefix').' '.$days.' '.jLocale::get('twitter.date.suffix.days');
            }
            
        }
        
        return $date;
    }
    
    
    private static function _hoursAgo($time1){
        $time2 = time();
            if( $time1 > $time2 ) {
                                $time = $time1 - $time2;
            } else {
                                $time = $time2 - $time1;
            }
             
            $time = $time / 3600;
            return round($time);
    }
    
    private static function _daysAgo($time1){
        $time2 = time();
            if( $time1 > $time2 ) {
                                $time = $time1 - $time2;
            } else {
                                $time = $time2 - $time1;
            }
             
            $time = $time / (3600*24);
            return round($time);
    }    
    
}
