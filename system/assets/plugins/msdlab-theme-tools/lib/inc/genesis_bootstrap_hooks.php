<?php
class MSDLab_Genesis_Bootstrap
{

    //Properties
    private $options;

    //Methods
    function __construct($options)
    {
        /*** Bootstrappin **/
        $defaults = array(
            'sidebar' => array(
                'xs' => 0,
                'sm' => 0,
                'md' => 4,
                'lg' => 4
            ),
            'sidebar_alt' => array(
                'xs' => 0,
                'sm' => 0,
                'md' => 4,
                'lg' => 4
            ),
        );

        $this->options = wp_parse_args($options, $defaults);


        add_action('admin_print_styles', array(&$this,'add_admin_styles'));

        add_filter('genesis_attr_site-inner', array(&$this,'msdlab_bootstrap_site_inner'), 10);
        add_filter('genesis_attr_breadcrumb', array(&$this,'msdlab_bootstrap_breadcrumb'), 10);
        add_filter('genesis_attr_content-sidebar-wrap', array(&$this,'msdlab_bootstrap_content_sidebar_wrap'), 10);
        add_filter('genesis_attr_content', array(&$this,'msdlab_bootstrap_content'), 10);
        add_filter('genesis_attr_sidebar-primary', array(&$this,'msdlab_bootstrap_sidebar'), 10);
        add_filter('genesis_attr_sidebar-secondary', array(&$this,'msdlab_bootstrap_sidebar_alt'), 10);
    }

    /*** Bootstrappin **/

    function msdlab_bootstrap_site_inner($attributes)
    {
        $attributes['class'] .= ' container';
        return $attributes;
    }

    function msdlab_bootstrap_breadcrumb($attributes)
    {
        $attributes['class'] .= ' wrap';
        return $attributes;
    }

    function msdlab_bootstrap_content_sidebar_wrap($attributes)
    {
        $layout = genesis_site_layout();
        switch ($layout) {
            case 'content-sidebar':
            case 'sidebar-content':
                $attributes['class'] .= ' row';
                break;
            case 'content-sidebar-sidebar':
            case 'sidebar-sidebar-content':
            case 'sidebar-content-sidebar':
                foreach($this->options['sidebar'] AS $k => $v){
                    $attributes['class'] .= ' col-'.$k.'-'.(12-$v);
                }
                break;
            case 'full-width-content':
                $attributes['class'] .= ' row';
                break;
        }
        return $attributes;
    }

    function msdlab_bootstrap_content($attributes)
    {
        $layout = genesis_site_layout();
        $template = get_page_template();
        switch ($layout) {
            case 'content-sidebar':
            case 'sidebar-content':
                foreach($this->options['sidebar'] AS $k => $v){
                    if($v!=12) {
                        $attributes['class'] .= ' col-' . $k . '-' . (12 - $v);
                    } else {
                        $attributes['class'] .= ' col-' . $k . '-12';
                    }
                }
                break;
            case 'content-sidebar-sidebar':
            case 'sidebar-sidebar-content':
            case 'sidebar-content-sidebar':
                foreach($this->options['sidebar_alt'] AS $k => $v){
                    $attributes['class'] .= ' col-'.$k.'-'.(12-$v);
                }
                break;
            case 'full-width-content':
                $attributes['class'] .= ' col-md-12';
                break;
        }
        return $attributes;
    }

    function msdlab_bootstrap_sidebar($attributes)
    {
        $layout = genesis_site_layout();
        $template = get_page_template();
        switch ($layout) {
            case 'content-sidebar':
            case 'sidebar-content':
            case 'content-sidebar-sidebar':
            case 'sidebar-sidebar-content':
            case 'sidebar-content-sidebar':
                foreach($this->options['sidebar'] AS $k => $v){
                    if($v == 0){
                        $attributes['class'] .= ' hidden-' . $k;
                    } else {
                        $attributes['class'] .= ' col-' . $k . '-' . $v;
                    }
                }
                break;
            case 'full-width-content':
                $attributes['class'] .= ' hidden';
                break;
        }
        return $attributes;
    }

    function msdlab_bootstrap_sidebar_alt($attributes)
    {
        $layout = genesis_site_layout();
        $template = get_page_template();
        switch ($layout) {
            case 'content-sidebar':
            case 'sidebar-content':
            case 'content-sidebar-sidebar':
            case 'sidebar-sidebar-content':
            case 'sidebar-content-sidebar':
                foreach($this->options['sidebar_alt'] AS $k => $v){
                    if($v == 0){
                        $attributes['class'] .= ' hidden-' . $k;
                    } else {
                        $attributes['class'] .= ' col-' . $k . '-' . $v;
                    }
                }                break;
            case 'full-width-content':
                $attributes['class'] .= ' hidden';
                break;
        }
        return $attributes;
    }

    function add_admin_styles(){
        wp_enqueue_style('bs3_overrides',plugin_dir_url(dirname(__FILE__)).'css/bootstrap-overrides.css');
    }

}