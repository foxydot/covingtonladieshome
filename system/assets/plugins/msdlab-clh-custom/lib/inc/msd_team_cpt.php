<?php
/**
 * Created by PhpStorm.
 * User: CMO
 * Date: 9/18/17
 * Time: 8:14 PM
 */
if (!class_exists('MSDTeamCPT')) {
    class MSDTeamCPT {
        //Properties
        var $cpt = 'team';
        //Methods
        /**
         * PHP 4 Compatible Constructor
         */
        public function MSDTeamCPT(){$this->__construct();}

        /**
         * PHP 5 Constructor
         */
        function __construct(){
            global $current_screen;
            //Actions
            add_action( 'init', array(&$this,'register_taxonomies') );
            add_action( 'init', array(&$this,'register_cpt') );
            add_action( 'init', array(&$this,'register_metaboxes') );
            //add_action('admin_head', array(&$this,'plugin_header'));
            add_action('admin_print_scripts', array(&$this,'add_admin_scripts') );
            add_action('admin_print_styles', array(&$this,'add_admin_styles') );
            add_action('admin_footer',array(&$this,'info_footer_hook') );
            // important: note the priority of 99, the js needs to be placed after tinymce loads
            add_action('admin_print_footer_scripts',array(&$this,'print_footer_scripts'),99);
            add_action('template_redirect', array(&$this,'my_theme_redirect'));
            add_action('admin_head', array(&$this,'codex_custom_help_tab'));

            //Filters
            add_filter( 'pre_get_posts', array(&$this,'custom_query') );
            add_filter( 'enter_title_here', array(&$this,'change_default_title') );
        }


        function register_taxonomies(){

            $labels = array(
                'name' => _x( 'Team categories', 'team-category' ),
                'singular_name' => _x( 'Team category', 'team-category' ),
                'search_items' => _x( 'Search team categories', 'team-category' ),
                'popular_items' => _x( 'Popular team categories', 'team-category' ),
                'all_items' => _x( 'All team categories', 'team-category' ),
                'parent_item' => _x( 'Parent team category', 'team-category' ),
                'parent_item_colon' => _x( 'Parent team category:', 'team-category' ),
                'edit_item' => _x( 'Edit team category', 'team-category' ),
                'update_item' => _x( 'Update team category', 'team-category' ),
                'add_new_item' => _x( 'Add new team category', 'team-category' ),
                'new_item_name' => _x( 'New team category name', 'team-category' ),
                'separate_items_with_commas' => _x( 'Separate team categories with commas', 'team-category' ),
                'add_or_remove_items' => _x( 'Add or remove team categories', 'team-category' ),
                'choose_from_most_used' => _x( 'Choose from the most used team categories', 'team-category' ),
                'menu_name' => _x( 'Team categories', 'team-category' ),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'show_tagcloud' => false,
                'hierarchical' => true, //we want a "category" style taxonomy, but may have to restrict selection via a dropdown or something.

                'rewrite' => array('slug'=>'team-category','with_front'=>false),
                'query_var' => true
            );

            register_taxonomy( 'team_category', array($this->cpt), $args );
        }

        function register_cpt() {

            $labels = array(
                'name' => _x( 'Team Members', 'team' ),
                'singular_name' => _x( 'Team Member', 'team' ),
                'add_new' => _x( 'Add New', 'team' ),
                'add_new_item' => _x( 'Add New Team Member', 'team' ),
                'edit_item' => _x( 'Edit Team Member', 'team' ),
                'new_item' => _x( 'New Team Member', 'team' ),
                'view_item' => _x( 'View Team Member', 'team' ),
                'search_items' => _x( 'Search Team Members', 'team' ),
                'not_found' => _x( 'No team members found', 'team' ),
                'not_found_in_trash' => _x( 'No team members found in Trash', 'team' ),
                'parent_item_colon' => _x( 'Parent Team:', 'team' ),
                'menu_name' => _x( 'Team Members', 'team' ),
            );

            $args = array(
                'labels' => $labels,
                'hierarchical' => false,
                'description' => 'Team',
                'supports' => array( 'title', 'editor', 'thumbnail' ),
                'taxonomies' => array( 'team_category' ),
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
                'rewrite' => array('slug'=>'team','with_front'=>false),
                'capability_type' => 'post',
                'menu_icon' => 'dashicons-groups',
            );

            register_post_type( $this->cpt, $args );
        }


        function register_metaboxes(){
            global $team_info,$contact_info;
            $team_info = new WPAlchemy_MetaBox(array
            (
                'id' => '_team_information',
                'title' => 'Team Member Info',
                'types' => array($this->cpt),
                'context' => 'normal',
                'priority' => 'high',
                'template' => plugin_dir_path(dirname(__FILE__)).'template/metabox-team.php',
                'autosave' => TRUE,
                'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                'prefix' => '_team_' // defaults to NULL
            ));
            $contact_info = new WPAlchemy_MetaBox(array
            (
                'id' => '_contact_information',
                'title' => 'Contact Info',
                'types' => array($this->cpt),
                'context' => 'normal',
                'priority' => 'high',
                'template' => plugin_dir_path(dirname(__FILE__)).'template/metabox-contact.php',
                'autosave' => TRUE,
                'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
                'prefix' => '_contact_' // defaults to NULL
            ));
        }


        function add_admin_scripts() {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
            }
        }

        function add_admin_styles() {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                wp_enqueue_style('custom_meta_css',plugin_dir_url(dirname(__DIR__)).'css/meta.css');
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
							$(\'#postdivrich\').prepend(\'<h2>Team Member Bio</h2>\');
					});
				/* ]]> */</script>';
            }
        }

        function info_footer_hook()
        {
            global $current_screen;
            if($current_screen->post_type == $this->cpt){
                ?><script type="text/javascript">
                    jQuery('#postdivrich').before(jQuery('#_contact_information_metabox'));
                    jQuery('#_contact_information_metabox').before(jQuery('#_team_information_metabox'));
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
                        $return_template = plugin_dir_path(dirname(__DIR__)). 'template/' . $templatefilename;
                    }
                    do_theme_redirect($return_template);

                    //A Custom Taxonomy Page
                } elseif ($wp->query_vars["taxonomy"] == 'team_category') {
                    $templatefilename = 'taxonomy-team_category.php';
                    if (file_exists(STYLESHEETPATH . '/' . $templatefilename)) {
                        $return_template = STYLESHEETPATH . '/' . $templatefilename;
                    } else {
                        $return_template = plugin_dir_path(dirname(__DIR__)) . 'template/' . $templatefilename;
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
                return __('Team Member Name','team');
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