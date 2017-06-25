<?php


/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package   Samadhan\LearnDash
 * @subpackage Ld_sat_scorer/includes
 * @author     Samadhan
 */

namespace Samadhan\LearnDash\ScoreConverter;

class Scorer
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Ld_Sat_Scorer_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->pluginName = 'smdn-ld-score-converter';
        $this->version = '1.0.0';

        $this->loadDependencies();
        $this->setLocale();
        $this->defineAdminHooks();
      //  $this->definePublicHooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function loadDependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-score-converter-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-score-converter-i18n.php';

         /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-score-converter-admin.php';


        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-score-converter-course.php';



        require_once plugin_dir_path(dirname(__FILE__)) . '/admin/class-score-converter-course-edit.php' ;
        $this->course_scale_edit = new \Samadhan\LearnDash\ScoreConverter\Scorer_Admin_Course_Edit();


        $this->loader = new  \Samadhan\LearnDash\ScoreConverter\ScorerLoader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Ld_Content_Cloner_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function setLocale()
    {
        $plugin_i18n = new \Samadhan\LearnDash\ScoreConverter\Scoreri18n();
        $plugin_i18n->setDomain($this->getPluginName());

        $this->loader->addAction('plugins_loaded', $plugin_i18n, 'loadPluginTextdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function defineAdminHooks()
    {


        
        $plugin_admin = new \Samadhan\LearnDash\ScoreConverter\ScorerAdmin($this->getPluginName(), $this->getVersion());
        $ld_course = new \Samadhan\LearnDash\ScoreConverter\ScorerCourse();


        /*
        $this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueueStyles');
        $this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueueScripts');
        $this->loader->addFilter('page_row_actions', $ld_course, 'addScorerCourseRowActions', 10, 2);
        $this->loader->addAction('parse_request',  $ld_course, 'getCourseID', 10, 2);


            if (@version_compare(LEARNDASH_VERSION, '2.2.1', '<')) {
                $this->loader->addFilter('page_row_actions', $ld_course, 'addCourseRowActions', 10, 2);
                $this->loader->addFilter('page_row_actions', $ld_course, 'addLessonRowActions', 10, 2);
            } else {
                $this->loader->addFilter('post_row_actions', $ld_course, 'addCourseRowActions', 10, 2);
                $this->loader->addFilter('post_row_actions', $ld_course, 'addLessonRowActions', 10, 2);
            }

        */
    }






    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function getPluginName()
    {
        return $this->pluginName;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Ld_Content_Cloner_Loader    Orchestrates the hooks of the plugin.
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function getVersion()
    {
        return $this->version;
    }
}
