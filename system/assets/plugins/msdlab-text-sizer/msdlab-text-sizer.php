<?php
/*
Plugin Name: MSDLab Text Sizer
Description: Javascript widget to resize text for senior accessibility.
Version: 0.1
Author: MSDLab
Author URI: http://msdlab.com/
License: GPL v2
*/

class MSDLabTextSizer
{
    private $ver;

    function MSDLabTextSizer()
    {
        $this->__construct();
    }

    function __construct()
    {
        $this->ver = '0.1';
        add_shortcode('text_resizer',@array($this,'display_text_resizer'));
        if($this->genesis_test()) {
            add_action('genesis_before_loop',@array($this,'display_text_resizer'));
        }
        add_action('wp_footer',@array($this,'add_scripts'));
    }

    function add_scripts(){
        $js = "<script>
        jQuery(document).ready(function($) {
            var size = parseInt($('html').css('font-size'));
            $('.text-sizer .plus').click(function(){
                size = size*1.05;
                $('html').css('font-size',size + 'px');
            });
            $('.text-sizer .minus').click(function(){
                size = size*0.95;
                $('html').css('font-size',size + 'px');
            });
        });
        </script>
        <style>
        .text-sizer{
            margin: 1em 0 0 0;
            display: block;
            z-index: 90;
            color: rgba(0,0,0,0.4);
            text-align: right;
         }
        .text-sizer div{
            border: 1px solid rgba(0,0,0,0.2);
            background: rgba(255,255,255,0.2);
            width: auto;
            padding: 0.25em .5em;
            display: inline-block;
        }
        .text-sizer div i{
            margin-left: 0.5em;
            padding: 0.25em;
            color: rgba(0,0,0,0.4);
            cursor: pointer;
            font-size:.9em;
        }
        </style>";
        print $js;
    }

    function construct_text_resizer($atts){
        extract( shortcode_atts( array(
            'class' => '',
        ), $atts ) );
        $ret = "<div class=\"text-sizer\"><div>Font Size <i class=\"minus fa fa-minus\"></i><i class=\"plus fa fa-plus\"></i></div></div>";
        return $ret;
    }

    function display_text_resizer($atts){
        print $this->construct_text_resizer($atts);
    }

    function genesis_test(){
        $theme_info = wp_get_theme();

        $genesis_flavors = array(
            'genesis',
            'genesis-trunk',
        );

        if ( ! in_array( $theme_info->Template, $genesis_flavors ) ) {
            return false;
        } else {
            return true;
        }
    }
}
//instantiate
new MSDLabTextSizer();

/*
 * //add element to page
    $('article.first-child').prepend('<div class="text-sizer"><div>Font Size <i class="minus fa fa-minus"></i><i class="plus fa fa-plus"></i></div></div>');
    //$('.text-sizer').sticky({topSpacing:210});
    var size = parseInt($('html').css('font-size'));
    $('.text-sizer .plus').click(function(){
        size = size*1.05;
        $('html').css('font-size',size + 'px');
    });
    $('.text-sizer .minus').click(function(){
        size = size*0.95;
        $('html').css('font-size',size + 'px');
    });
 */