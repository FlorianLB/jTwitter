<?php
/**
*@package jTwitter
* @author    Florian Lonqueu-Brochard
* @copyright 2010-2011 Florian Lonqueu-Brochard
* @license    MIT
*/

class testCtrl extends jController {
    /**
    *
    */
    function index() {
        $rep = $this->getResponse('html');

        $rep->title = "jTwitter";
        
        $content ='<h2>jTwitter plugin</h2>';
        
        $content .= jZone::get('jTwitter~timeline', array('user' => 'firefox', 'count' => 5));

        $rep->body->assign('MAIN', $content);

        return $rep;
    }
}

