<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Ld_Content_Cloner
 * @subpackage Ld_Content_Cloner/includes
 */



namespace Samadhan\LearnDash\ScoreConverter;


class ScorerCourse
{

    protected static $course_id=0;
    protected static $new_course_id=0;

    /**
     *
     * @since    1.0.0
     */

    public function __construct()
    {
    }





    public function getCourseID($query){

        $course_id = isset( $_REQUEST['course_id'] ) ? $_REQUEST['course_id'] : 0;

    }



    public function addCourseRowActions($actions, $post_data)
    {

        if (get_post_type($post_data->ID) === 'sfwd-courses') {
            $actions = array_merge(
                $actions,
                array(
                    'greade_course' => '<a href="?post_type=ld_sat_scorer&course_id=' . $post_data->ID . '"  title="Scale score of this course" class="samadhan-score-course" data-course-id="' . $post_data->ID . '" data-course="' . wp_create_nonce('grade_course_' . $post_data->ID) . '">' . __('Scale Course Score') . '</a>'
                )
            );
        }

        return $actions;
    }



    public function addLessonRowActions($actions, $post_data)
    {



        if (get_post_type($post_data->ID) === 'sfwd-lessons') {

            $actions = array_merge(
                $actions,
                array(
                    'grade_lesson' => '<a href="#" title="Scale score of this lesson" class="ldcc-clone-lesson" data-lesson-id="' . $post_data->ID . '" >' . __('Scale Lesson Score') . '</a>'
                )
            );

        } elseif (get_post_type($post_data->ID) === 'sfwd-quiz') {
            $actions = array_merge(
                $actions,
                array(
                            'clone_quiz' => '<a href="#" title="Clone quiz" class="ldcc-clone-quiz" data-quiz-id="' . $post_data->ID . '" data-course-id="'.get_post_meta($post_data->ID, 'course_id', true).'">' . __('Clone Quiz') . '</a>'
                        )
            );
        }
        return $actions;
    }




}
