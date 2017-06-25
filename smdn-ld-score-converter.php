<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://Samadhan.com.au
 * @since             1.0.0
 * @package           Samadhan_LearnDash_Score_Converter
 * @wordpress-plugin
 * Plugin Name:       Samadhan LearnDash Score Converter
 * Plugin URI:        http://samadhan.com.au
 * Description:       This plugin scores LearnDash course grades to SAT Style Grades.
 * Version:           1.0.1
 * Author:            Samadhan
 * Author URI:        http://samadhan.com.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smdn-ld-score-converter
 * Domain Path:       /languages
 */


$course_formula_post_type = "smdn-ct-course";
$lesson_formula_post_type = "smdn-ct-lesson";


// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

//add_action('init', 'register_ld_sat_scorer');
add_action('plugins_loaded','load_smdn_ld_score_converter_Plugin');
//add_filter('ld_after_course_status_template_container','add_hello',99);





function load_smdn_ld_score_converter_Plugin()
{
    run_smdn_ld_score_converter();
}



/**
 * The code that runs during plugin activation.
 */

function activate_smdn_ld_score_converter()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-score-converter-activator.php';
     \Samadhan\LearnDash\ScoreConverter\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_smdn_ld_score_converter()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-score-converter-deactivator.php';
    \Samadhan\LearnDash\ScoreConverter\Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_smdn_ld_score_converter');
register_deactivation_hook(__FILE__, 'deactivate_smdn_ld_score_converter');


require_once plugin_dir_path(__FILE__) . 'functions.php';

require plugin_dir_path(__FILE__) . 'includes/class-score-converter.php';

//require_once plugin_dir_path(__FILE__) . 'helpers/class-ld-sat-template-loader.php';
require_once plugin_dir_path(__FILE__) . 'helpers/class-score-converter-custom-post-types-manager.php';
//require_once plugin_dir_path(__FILE__) . 'models/class-score-converter-model-course.php';
require_once plugin_dir_path(__FILE__) . 'models/class-score-converter-model-lesson.php';

$ld_sat_custom_post_types_manager = new \Samadhan\LearnDash\ScoreConverter\Custom_Post_Types_Manager();
//$ld_sat_template_loader = new \Samadhan\LearnDash\ScoreConverter\Template_Loader();

$template_path =  plugin_dir_path(__FILE__) . 'templates/score-converter-meta-template.php';

function run_smdn_ld_score_converter()
{
    $plugin = new Samadhan\LearnDash\ScoreConverter\Scorer();
    $plugin->run();
}

