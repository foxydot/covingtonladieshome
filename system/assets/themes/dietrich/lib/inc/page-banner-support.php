<?php
if(!class_exists('WPAlchemy_MetaBox')){
    include_once (WP_CONTENT_DIR.'/wpalchemy/MetaBox.php');
}

global $wpalchemy_media_access;
if(!class_exists('WPAlchemy_MediaAccess')){
    include_once (WP_CONTENT_DIR.'/wpalchemy/MediaAccess.php');
}
$wpalchemy_media_access = new WPAlchemy_MediaAccess();

if (!class_exists('MSDLab_Page_Banner_Support')) {
    class MSDLab_Page_Banner_Support {
        //Properties
        private $options;

        //Methods
        /**
         * PHP 4 Compatible Constructor
         */
        public function MSDLab_Page_Banner_Support(){$this->__construct();}

        /**
         * PHP 5 Constructor
         */
        function __construct($options){
            global $current_screen;
            //"Constants" setup
            //Actions
            add_action( 'init', array(&$this,'register_metaboxes') );
            add_action('admin_print_styles', array(&$this,'add_admin_styles') );
            add_action('admin_footer',array(&$this,'footer_hook') );
            add_action('msdlab_title_area',array(&$this,'msdlab_do_page_banner') );

            //Filters
        }

        function register_metaboxes(){
            global $page_banner_metabox;
            $page_banner_metabox = new WPAlchemy_MetaBox(array
            (
                'id' => '_page_banner',
                'title' => 'Page Banner Area',
                'types' => array('post','page','event'),
                'context' => 'normal', // same as above, defaults to "normal"
                'priority' => 'high', // same as above, defaults to "high"
                'template' => get_stylesheet_directory().'/lib/template/metabox-page_banner.php',
                'autosave' => TRUE,
                'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                'prefix' => '_msdlab_' // defaults to NULL
            ));
        }

        function add_admin_styles() {
            //wp_enqueue_style('custom_meta_css',plugin_dir_url(dirname(__FILE__)).'css/meta.css');
        }

        function footer_hook()
        {
            ?><script type="text/javascript">
            jQuery('#titlediv').after(jQuery('#_page_banner_metabox'));
        </script><?php
        }

        function msdlab_do_page_banner(){
            if(is_page()){
                global $post, $page_banner_metabox;
                $page_banner_metabox->the_meta();
                $bannerbool = $page_banner_metabox->get_the_value('bannerbool');
                if($bannerbool != 'true'){
                    return;
                }
                $bannerclass = $page_banner_metabox->get_the_value('bannerclass');
                $banneralign = $page_banner_metabox->get_the_value('banneralign');
                $bannerimage = $page_banner_metabox->get_the_value('bannerimage');
                $bannercontent = apply_filters('the_content',$page_banner_metabox->get_the_value('bannercontent'));
                remove_action('genesis_entry_header','genesis_do_post_title');
                global $post;
                $background = strlen($bannerimage)>0?' style="background-image:url('.$bannerimage.')"':'';
                print '<div class="banner clearfix '.$banneralign.' '.$bannerclass.'">';
                print '<div class="wrap"'.$background.'>';
                print '<div class="gradient">';
                print '<div class="bannertext">';
                print genesis_do_post_title();
                print '<div class="bannercontent">'.$bannercontent.'</div>';
                print '</div>';
                print '</div>';
                print '</div>';
                print '</div>';
            } else {
                genesis_do_post_title();
            }
        }

    } //End Class
} //End if class exists statement