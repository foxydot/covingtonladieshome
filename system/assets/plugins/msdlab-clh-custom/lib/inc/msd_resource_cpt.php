<?php
if (!class_exists('MSDResourceCPT')) {
    class MSDResourceCPT {
        //Properties
        var $cpt = 'resource';
        //Methods
        /**
         * PHP 4 Compatible Constructor
         */
        public function MSDResourceCPT(){$this->__construct();}

        /**
         * PHP 5 Constructor
         */
        function __construct(){
            global $current_screen;
            //"Constants" setup
            $this->plugin_url = plugin_dir_url('msd-custom-cpt/msd-custom-cpt.php');
            $this->plugin_path = plugin_dir_path('msd-custom-cpt/msd-custom-cpt.php');
            //Actions
            add_action( 'init', array(&$this,'register_taxonomies') );
            add_action( 'init', array(&$this,'register_cpt') );
            add_action( 'init', array(&$this,'register_metaboxes') );
            add_action('admin_head', array(&$this,'plugin_header'));
            add_action('admin_print_scripts', array(&$this,'add_admin_scripts') );
            add_action('admin_print_styles', array(&$this,'add_admin_styles') );
            //add_action('admin_footer',array(&$this,'info_footer_hook') );
            // important: note the priority of 99, the js needs to be placed after tinymce loads
            add_action('admin_print_footer_scripts',array(&$this,'print_footer_scripts'),99);
            add_action('template_redirect', array(&$this,'my_theme_redirect'));
            add_action('admin_head', array(&$this,'codex_custom_help_tab'));

            //Filters
            add_filter( 'pre_get_posts', array(&$this,'custom_query') );
            //add_filter( 'enter_title_here', array(&$this,'change_default_title') );
        }


        function register_taxonomies(){

            $labels = array(
                'name' => _x( 'Press categories', 'resource-category' ),
                'singular_name' => _x( 'Press category', 'resource-category' ),
                'search_items' => _x( 'Search press categories', 'resource-category' ),
                'popular_items' => _x( 'Popular press categories', 'resource-category' ),
                'all_items' => _x( 'All press categories', 'resource-category' ),
                'parent_item' => _x( 'Parent press category', 'resource-category' ),
                'parent_item_colon' => _x( 'Parent press category:', 'resource-category' ),
                'edit_item' => _x( 'Edit press category', 'resource-category' ),
                'update_item' => _x( 'Update press category', 'resource-category' ),
                'add_new_item' => _x( 'Add new press category', 'resource-category' ),
                'new_item_name' => _x( 'New press category name', 'resource-category' ),
                'separate_items_with_commas' => _x( 'Separate press categories with commas', 'resource-category' ),
                'add_or_remove_items' => _x( 'Add or remove press categories', 'resource-category' ),
                'choose_from_most_used' => _x( 'Choose from the most used press categories', 'resource-category' ),
                'menu_name' => _x( 'Press categories', 'resource-category' ),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'show_tagcloud' => false,
                'hierarchical' => true, //we want a "category" style taxonomy, but may have to restrict selection via a dropdown or something.

                'rewrite' => array('slug'=>'press-category','with_front'=>false),
                'query_var' => true
            );

            register_taxonomy( 'press_category', array($this->cpt), $args );
        }

        function register_cpt() {

            $labels = array(
                'name' => _x( 'Press', 'resource' ),
                'singular_name' => _x( 'Press', 'resource' ),
                'add_new' => _x( 'Add New', 'resource' ),
                'add_new_item' => _x( 'Add New Press', 'resource' ),
                'edit_item' => _x( 'Edit Press', 'resource' ),
                'new_item' => _x( 'New Press', 'resource' ),
                'view_item' => _x( 'View Press', 'resource' ),
                'search_items' => _x( 'Search Press', 'resource' ),
                'not_found' => _x( 'No press found', 'resource' ),
                'not_found_in_trash' => _x( 'No press found in Trash', 'resource' ),
                'parent_item_colon' => _x( 'Parent Press:', 'resource' ),
                'menu_name' => _x( 'Press', 'resource' ),
            );

            $args = array(
                'labels' => $labels,
                'hierarchical' => false,
                'description' => 'Resource',
                'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' ),
                'taxonomies' => array( 'press_category' ),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 20,

                'show_in_nav_menus' => true,
                'publicly_queryable' => true,
                'exclude_from_search' => true,
                'has_archive' => true,
                'query_var' => true,
                'can_export' => true,
                'rewrite' => array('slug'=>'resource','with_front'=>false),
                'capability_type' => 'post',
                'menu_icon' => 'dashicons-portfolio',
            );

            register_post_type( $this->cpt, $args );
        }


        function register_metaboxes(){
            global $resource_info;
            $resource_info = new WPAlchemy_MetaBox(array
            (
                'id' => '_resource_information',
                'title' => 'Press Info',
                'types' => array($this->cpt),
                'context' => 'normal',
                'priority' => 'high',
                'template' => plugin_dir_path(dirname(__FILE__)).'/template/metabox-resource.php',
                'autosave' => TRUE,
                'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                'prefix' => '_resource_' // defaults to NULL
            ));
        }


        function add_admin_scripts() {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                wp_enqueue_script('bootstrap-jquery','//maxcdn.bootstrapcdn.com/bootstrap/latest/js/bootstrap.min.js',array('jquery'),$this->ver,TRUE);
                wp_enqueue_script('timepicker-jquery',plugin_dir_url(dirname(__FILE__)).'/js/jquery.timepicker.min.js',array('jquery'),$this->ver,FALSE);
                wp_enqueue_script( 'jquery-ui-datepicker' );
                wp_enqueue_script('msdsocial-jquery',plugin_dir_url(dirname(__FILE__)).'/js/plugin-jquery.js',array('jquery','timepicker-jquery'),$this->ver,TRUE);
            }
        }

        function add_admin_styles() {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                wp_register_style('timepicker-style',plugin_dir_url(dirname(__FILE__)).'/css/jquery.timepicker.css');
                wp_enqueue_style('timepicker-style');
                wp_enqueue_style('jqueryui-smoothness','//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
            }
        }

        function print_footer_scripts()
        {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                print '<script type="text/javascript">/* <![CDATA[ */
					jQuery(function($)
					{
						var i=1;
						$(\'.customEditor textarea\').each(function(e)
						{
							var id = $(this).attr(\'id\');
			 
							if (!id)
							{
								id = \'customEditor-\' + i++;
								$(this).attr(\'id\',id);
							}
			 
							tinyMCE.execCommand(\'mceAddControl\', false, id);
			 
						});
					});
				/* ]]> */</script>';
            }
        }

        function info_footer_hook()
        {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                ?><script type="text/javascript">
                </script><?php
            }
        }


        function my_theme_redirect() {
            global $wp;

            //A Specific Custom Post Type
            if ($wp->query_vars["post_type"] == $this->cpt) {
                if(is_single()){
                    $templatefilename = 'single-'.$this->cpt.'.php';
                    if (file_exists(STYLESHEETPATH . '/' . $templatefilename)) {
                        $return_template = STYLESHEETPATH . '/' . $templatefilename;
                    } else {
                        $return_template = plugin_dir_path(dirname(__FILE__)). 'template/' . $templatefilename;
                    }
                    do_theme_redirect($return_template);

                    //A Custom Taxonomy Page
                } elseif ($wp->query_vars["taxonomy"] == 'resource_category') {
                    $templatefilename = 'taxonomy-resource_category.php';
                    if (file_exists(STYLESHEETPATH . '/' . $templatefilename)) {
                        $return_template = STYLESHEETPATH . '/' . $templatefilename;
                    } else {
                        $return_template = plugin_dir_path(dirname(__FILE__)) . 'template/' . $templatefilename;
                    }
                    do_theme_redirect($return_template);
                }
            }
        }

        function codex_custom_help_tab() {
            global $current_screen;
            if($current_screen->post_type != $this->cpt)
                return;

            // Setup help tab args.
            $args = array(
                'id'      => 'title', //unique id for the tab
                'title'   => 'Title', //unique visible title for the tab
                'content' => '<h3>The Event Title</h3>
                          <p>The title of the event.</p>
                          <h3>The Permalink</h3>
                          <p>The permalink is created by the title, but it doesn\'t change automatically if you change the title. To change the permalink when editing an event, click the [Edit] button next to the permalink. 
                          Remove the text that becomes editable and click [OK]. The permalink will repopulate with the new Location and date!</p>
                          ',  //actual help text
            );

            // Add the help tab.
            $current_screen->add_help_tab( $args );

            // Setup help tab args.
            $args = array(
                'id'      => 'event_info', //unique id for the tab
                'title'   => 'Event Info', //unique visible title for the tab
                'content' => '<h3>Event URL</h3>
                          <p>The link to the page describing the event</p>
                          <h3>The Event Date</h3>
                          <p>The Event Date is the date of the event. This value is restrained to dates (chooseable via a datepicker module). This value is also used to sort events for the calendars, upcoming events, etc.</p>
                          <p>For single day events, set start and end date to the same date.',  //actual help text
            );

            // Add the help tab.
            $current_screen->add_help_tab( $args );

        }


        function custom_query( $query ) {
            if(!is_admin()){
                if($query->is_main_query() && $query->is_search){
                    $searchterm = $query->query_vars['s'];
                    // we have to remove the "s" parameter from the query, because it will prevent the posts from being found
                    $query->query_vars['s'] = "";

                    if ($searchterm != "") {
                        $query->set('meta_value',$searchterm);
                        $query->set('meta_compare','LIKE');
                    };
                    $query->set( 'post_type', array('post','page',$this->cpt) );
                    ts_data($query);
                }
                elseif( $query->is_main_query() && $query->is_archive ) {
                    $query->set( 'post_type', array('post','page',$this->cpt) );
                }
            }
        }

        function change_default_title( $title ){
            global $current_screen;
            if  ( $current_screen->post_type == $this->cpt ) {
                return __('Resource Name','resource');
            } else {
                return $title;
            }
        }

        function cpt_display(){
            global $post;
            if(is_cpt($this->cpt)) {
                if (is_single()){
                    //display content here
                } else {
                    //display for aggregate here
                }
            }
        }
    } //End Class
} //End if class exists statement