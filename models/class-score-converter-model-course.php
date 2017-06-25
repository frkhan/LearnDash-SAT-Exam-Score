<?php

/*
 * Score Converter
 * application.
 *
 */

namespace Samadhan\LearnDash\ScoreConverter;

class ModelCourseScore {

    private $post_type;
    //private $template_parser;
    //private $score_converter_taxonomy;

    private $error_message;

    public $maxValue, $minValue, $totalQuestions;
    /*
     * Execute initiamizations for the score converter
     *
     * @param  object  Twig Template
     * @return -
    */
    public function __construct() {

        global $course_formula_post_type;

        $this->totalQuestions = 40;
        $this->maxValue = 40;
        $this->minValue = 10;

        $this->post_type =  $course_formula_post_type;// "smdn_ld_sc_course";
        //   $this->score_converter_taxonomy = "ld_sat_scorer_category";


        $this->error_message = "";

        // $this->template_parser = $ld_sat_template_loader;


        add_action( 'init', array( $this, 'create_score_converter_post_type' ) );

        //add_action( 'init', array( $this, 'create_score_converter_custom_taxonomies' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_score_converter_meta_boxes' ) );

        add_action( 'save_post', array( $this, 'save_score_converter_meta_data' ) );

        add_filter( 'post_updated_messages', array( $this, 'generate_score_converter_messages' ) );

        add_action( 'p2p_init', array( $this, 'join_formulae_to_courses' ) );

    }

    public function getPostType(){
        return  $this->post_type ;
    }

    /*
     * Register custom post type for score converter
     *
     * @param  -
     * @return -
    */
    public function create_score_converter_post_type() {

        global $ld_sat_custom_post_types_manager;

        $params = array();

        $params['post_type'] = $this->post_type;
        $params['singular_post_name'] = __('Course Score Converter',$this->post_type);
        $params['plural_post_name'] = __('LD Course Scorer',$this->post_type);
        $params['description'] = __('Raw Course Score to Scaled Score Conversion Charts',$this->post_type);
        $params['supported_fields'] = array('title', 'editor');

        $ld_sat_custom_post_types_manager->create_post_type($params);
    }

    /*
     * Register custom taxonomies for the score converter screen
     *
     * @param  -
     * @return -
    */
    /*
    public function create_score_converter_custom_taxonomies() {

        global $ld_sat_custom_post_types_manager;

        $params = array();

        $params['category_taxonomy'] = $this->score_converter_taxonomy;
        $params['post_type'] = $this->post_type;

        $params['singular_name'] = __('Conversion Chart','ld_sat_scorer');
        $params['plural_name'] = __('Conversion Charts','ld_sat_scorer');


        $ld_sat_custom_post_types_manager->create_custom_taxonomies($params);


    }
    */

    /*
     * Define the function for displaying custom meta box
     *
     * @param  -
     * @return -
    */
    public function add_score_converter_meta_boxes() {
        add_meta_box( 'ld-sat-score-converter-meta', __('Score Conversion Details', $this->post_type), array( $this, 'display_score_converter_meta_boxes' ), $this->post_type );
    }


    public function defautScoringFormula($maxValue, $minValue, $totalQuestions, $score){
        $slope = ($maxValue - $minValue)/ $totalQuestions;
        return ceil($minValue + $slope * $score);

    }
    /*
     * Display the custom meta fields for score converter creation screen
     *
     * @param  -
     * @return -
    */
    public function display_score_converter_meta_boxes() {

        global $post,$template_data, $scoring_chart;
        global $totalQuestionRows;

        $totalQuestionRows =  $this->totalQuestions/2;

        //   global $ld_sat_template_loader;
        global $template_path;

        $template_data['score_converter_meta_nonce']    = wp_create_nonce('ld-sat-score-converter-meta');

        //$scoring_chart = 'my scoring chart';

        for ($i = 0; $i <= $this->totalQuestions; ++$i) {
            $scoring_chart[$i]['id'] = 'score_converter_scale_' . strval ($i );
            $scoring_chart[$i]['name'] = 'score_converter_scale_' . strval ($i );
            $meta_id = 's:'  . strval ($i );
            $meta_value = get_post_meta( $post->ID, $meta_id, true );
            $scoring_chart[$i]['value'] =   (!empty ( $meta_value ) ? (int) esc_attr( $meta_value ) : $this->defautScoringFormula($this->maxValue, $this->minValue, $this->totalQuestions, $i));
        }




        ob_start();
        //$this->template_parser->get_template_part( 'score-converter','meta');
        //  $ld_sat_template_loader->get_template_part( 'score-converter','meta');



        if ( $overridden_template = locate_template( $template_path,true,true) ) {
            // locate_template() returns path to file
            // if either the child theme or the parent theme have overridden the template
            load_template( $overridden_template );
        } else {
            // If neither the child nor parent theme have overridden the template,
            // we load the template from the 'templates' sub-directory of the directory this file is in
            load_template( $template_path );
        }


        $display = ob_get_clean();
        echo $display;
    }

    /*
    * Save service custom fields to database with neccessary validations
    *
    * @param  -  WordPress generated default messages list
    * @return int  Post ID
   */
    public function save_score_converter_meta_data() {
        global $post;

        // verify nonce
        if ( !wp_verify_nonce($_POST['score_converter_meta_nonce'], 'ld-sat-score-converter-meta' ) ) {
            return $post->ID;
        }

        // check autosave
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return $post->ID;
        }

        // check permissions
        if ( $this->post_type == $_POST['post_type'] && current_user_can('edit_post', $post->ID ) ) {


            for ($i = 0; $i <= $this->totalQuestions; ++$i) {
                $meta_id[$i] = 's:'  . strval ($i );
                $meta_value[$i] =  (isset( $_POST['score_converter_scale_' . strval ($i ) ] ) ? (int) esc_attr( trim($_POST['score_converter_scale_' . strval ($i )] )) : '');
            }


            if ( empty( $post->post_title ) ) {
                $this->error_message .= __('Conversion chart name cannot be empty. <br/>',  $this->post_type );
            }



            if ( !empty( $this->error_message ) ) {
                remove_action( 'save_post', array( $this, 'save_score_converter_meta_data' ) );

                $post->post_status = 'draft';
                wp_update_post( $post );

                add_action( 'save_post', array( $this, 'save_score_meta_data' ) );

                $this->error_message = __('Conversion chart creation failed.<br/>',  $this->post_type) . $this->error_message;

                set_transient( $this->post_type."_error_message_$post->ID", $this->error_message, 60 * 10 );

            } else {

                for ($i = 0; $i <= $this->totalQuestions; ++$i) {
                    update_post_meta($post->ID,  $meta_id[$i],  $meta_value[$i]);
                }
            }
        } else {
            return $post->ID;
        }
    }

    /*
     * Customize the exising messages for Conversion Charts
     *
     * @param  array  WordPress generated default messages list
     * @return array  Modified messages list
    */
    public function generate_score_converter_messages( $messages ) {
        global $ld_sat_custom_post_types_manager;

        $params = array();

        $params['post_type'] = $this->post_type;

        $params['singular_name'] = __('Conversion chart', $this->post_type);
        $params['plural_name'] = __('Conversion charts', $this->post_type);


        $messages = $ld_sat_custom_post_types_manager->generate_messages($messages,$params);

        return $messages;
    }


    /*
 * Register a relatioshhip type between Projects and
 * Services using the Posts 2 Posts plugin
 *
 * @param  -
 * @return -
*/
    public function join_formulae_to_courses() {

        p2p_register_connection_type( array(
            'name'  => 'formulae_to_courses',
            'from'  => $this->post_type,
            'to'    => 'sfwd-courses',
            'admin_dropdown' => 'any',
            'admin_box' => array(
                'show' => 'any',
                'context' => 'advanced'
            ),
            'admin_column' => 'any',
            'from_labels' => array(
                'column_title' => 'Used for Courses'
            ),
            'to_labels' => array(
                'column_title' => 'Scoring Chart Used'
            ),
        ) );

    }
}
?>
