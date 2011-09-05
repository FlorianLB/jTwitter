<?php
/**
*@package jTwitter
* @author    Florian Lonqueu-Brochard
* @copyright 2010-2011 Florian Lonqueu-Brochard
* @license    MIT
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
    
}
