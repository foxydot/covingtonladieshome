<?php
/*
Plugin Name: MSDLab CLH Custom
Description: Custom functions for Covington Ladies Home.
Version: 0.1
Author: MSDLab
Author URI: http://msdlab.com/
License: GPL v2
*/

class MSDLabCLHCustom
{
    private $ver;

    function MSDLabCLHCustom()
    {
        $this->__construct();
    }

    function __construct()
    {
        $this->ver = '0.1';
        /*
         * Pull in some stuff from other files
         */
        require_once(plugin_dir_path(__FILE__) . 'lib/inc/event_calendar_widget.php');
        add_action('widgets_init', @array($this,'widgets_init'));
    }

    function widgets_init(){
        register_widget("MSDLab_Events_Calendar_Widget");
    }
}
//instantiate
new MSDLabCLHCustom();