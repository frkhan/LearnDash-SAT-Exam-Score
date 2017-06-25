<?php
/*
 * Managing the initialization of custom post specific functions
*/

namespace Samadhan\LearnDash\ScoreConverter;

class Custom_Post_Types_Manager {

    private $base_path;
    private $template_parser;
    private $courses;
    private $lessons;

    //private $post_type;
    private $error_message;


    public function __construct() {

        $this->base_path = plugin_dir_path(__FILE__);
        /* The constructor will do the job */
       // $this->courses = new \Samadhan\LearnDash\ScoreConverter\ModelCourseScore();
        $this->lessons = new \Samadhan\LearnDash\ScoreConverter\ModelLessonScore();

    }


    /*
     * Register custom post type 
     *
     * @param  -
     * @return -
    */
    public function create_post_type($params) {
        
        extract($params);

        $labels = array(
            'name'                  => sprintf( __( '%s', $post_type ), $plural_post_name),
            'singular_name'         => sprintf( __( '%s', $post_type ), $singular_post_name),
            'add_new'               => __( 'Add New', $post_type ),
            'add_new_item'          => sprintf( __( 'Add New %s ', $post_type ), $singular_post_name),
            'edit_item'             => sprintf( __( 'Edit %s ', $post_type ), $singular_post_name),
            'new_item'              => sprintf( __( 'New  %s ', $post_type ), $singular_post_name),
            'all_items'             => sprintf( __( 'All  %s ', $post_type ), $plural_post_name),
            'view_item'             => sprintf( __( 'View  %s ', $post_type ), $singular_post_name),
            'search_items'          => sprintf( __( 'Search  %s ', $post_type ), $plural_post_name),
            'not_found'             => sprintf( __( 'No  %s found', $post_type ), $plural_post_name),
            'not_found_in_trash'    => sprintf( __( 'No  %s  found in the Trash', $post_type ), $plural_post_name),
            'parent_item_colon'     => '',
            'menu_name'             => sprintf( __('%s', $post_type ), $plural_post_name),
        );

        $args = array(
            'labels'                => $labels,
            'hierarchical'          => true,
            'description'           => $description,
            'supports'              => $supported_fields,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'publicly_queryable'    => true,
            'exclude_from_search'   => false,
            'has_archive'           => true,
            'query_var'             => true,
            'can_export'            => true,
            'rewrite'               => true,
            'capability_type'       => 'post',

        );

        register_post_type( $post_type, $args );
    }

    /*
     * Register custom taxonomies for the custom post screens
     *
     * @param  -
     * @return -
    */
    public function create_custom_taxonomies($params) {
        
        extract($params);

        $capabilities = isset($capabilities) ? $capabilities : array();
        
        register_taxonomy(
                $category_taxonomy,
                $post_type,
                array(
                    'labels' => array(
                        'name'              => sprintf( __( '%s Category', $post_type ) , $singular_name),
                        'singular_name'     => sprintf( __( '%s Category', $post_type ) , $singular_name),
                        'search_items'      => sprintf( __( 'Search %s Category', $post_type ) , $singular_name),
                        'all_items'         => sprintf( __( 'All %s Category', $post_type ) , $singular_name),
                        'parent_item'       => sprintf( __( 'Parent %s Category', $post_type ) , $singular_name),
                        'parent_item_colon' => sprintf( __( 'Parent %s Category:', $post_type ) , $singular_name),
                        'edit_item'         => sprintf( __( 'Edit %s Category', $post_type ) , $singular_name),
                        'update_item'       => sprintf( __( 'Update %s Category', $post_type ) , $singular_name),
                        'add_new_item'      => sprintf( __( 'Add New %s Category', $post_type ) , $singular_name),
                        'new_item_name'     => sprintf( __( 'New %s Category Name', $post_type ) , $singular_name),
                        'menu_name'         => sprintf( __( '%s Category', $post_type ) , $singular_name),
                    ),
                    'hierarchical' => true,
                    'capabilities' => $capabilities ,
                )
        );

        
    }

    /*
     * Customize the exising messages for post types
     *
     * @param  array  WordPress generated default messages list
     * @return array  Modified messages list
    */
    public function generate_messages( $messages, $params ) {
        global $post, $post_ID;
        
        extract($params);

        // Get the temporary error message from database and WordPress generated
        // error no
        $this->error_message = get_transient( $post_type."_error_message_$post->ID" );
        $message_no = isset( $_GET['message'] ) ? (int) $_GET['message'] : '0';

        // Remove the temporary error message from database
        delete_transient( $post_type."_error_message_$post->ID" );

        if ( !empty( $this->error_message ) ) {
            // Override the default WordPress generated message with our own custom
            // message
            $messages[$post_type] = array( "$message_no" => $this->error_message );
        } else {

            // Customize the messages list 
            $messages[$post_type] = array(
                0 => '', // Unused. Messages start at index 1.
                1 => sprintf(__('%1$s updated. <a href="%2$s">View %3$s</a>', $post_type ),$singular_name, esc_url(get_permalink($post_ID)),singular_name),
                
                2 => __('Custom field updated.', $post_type ),
                
                3 => __('Custom field deleted.', $post_type ),
                
                4 => sprintf( __('%1$s updated.', $post_type ), $singular_name),
                
                5 => isset($_GET['revision']) ? sprintf(__('%1$s restored to revision from %2$s', $post_type ),$singular_name, wp_post_revision_title((int) $_GET['revision'], false)) : false,
                
                6 => sprintf(__('%1$s published. <a href="%2$s">View %3$s</a>', $post_type ),$singular_name, esc_url(get_permalink($post_ID)),$singular_name),
                
                7 => sprintf(__('%1$s saved.', $post_type ),$singular_name),
                
                8 => sprintf(__('%1$s submitted. <a target="_blank" href="%2$s">Preview %3$s</a>', $post_type ), $singular_name, esc_url(add_query_arg('preview', 'true', get_permalink($post_ID))), $singular_name),
                
                9 => sprintf(__('%1$s scheduled for: <strong>%2$s</strong>. <a target="_blank" href="%3$s">Preview %4$s</a>', $post_type ),
                        $singular_name,
                        date_i18n(__('M j, Y @ G:i'),strtotime($post->post_date)), 
                        esc_url(get_permalink($post_ID)),
                        $singular_name),
                
                10 => sprintf(__('%1$s draft updated. <a target="_blank" href="%2$s">Preview %3$s</a>', $post_type ), $singular_name,  esc_url(add_query_arg('preview', 'true', get_permalink($post_ID))), $singular_name),
            );
        }


        return $messages;
    }
}



?>