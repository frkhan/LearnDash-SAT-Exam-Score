<?php


 function get_LD_Converted_QuizScore($postId) {
     $score = 0;

     $userId = get_current_user_id();
     $quizId =  get_post_meta( $postId, 'quiz_pro_id', true );

     global $wpdb;

     $table_quiz_statistic =  $wpdb->prefix . "wp_pro_quiz_statistic";
     $table_quiz_statistic_ref =  $wpdb->prefix . "wp_pro_quiz_statistic_ref";



     $query = $wpdb->prepare("SELECT MAX(s.statistic_ref_id) AS correct_count FROM  {$table_quiz_statistic}  AS s,  {$table_quiz_statistic_ref} AS sf WHERE s.statistic_ref_id = sf.statistic_ref_id AND sf.quiz_id = %d AND sf.user_id = %d", $quizId, $userId);
     $statistic_ref_id =$wpdb->get_var($query);


     $query = $wpdb->prepare("SELECT SUM(correct_count) AS correct_count FROM  {$table_quiz_statistic}  AS s INNER JOIN {$table_quiz_statistic_ref} AS sf ON s.statistic_ref_id = sf.statistic_ref_id WHERE sf.quiz_id = %d AND sf.user_id = %d AND s.statistic_ref_id = %d", $quizId, $userId, $statistic_ref_id);

     $score =$wpdb->get_var($query);

     return IS_NULL( $score) ?  0 : $score;
 }


function get_LD_Converted_LessonScore($lessonId) {

    global $wpdb;

    $response = (object)[];
    $score = 0;
    $convertedScore = 0;

    $response->rawScore =$score;
    $response->convertedScore = $convertedScore;
    $userId = get_current_user_id();

    /********* ADD UP ALL THE QUIZ SCORES *****************************/
    $quizzes =learndash_get_lesson_quiz_list( $lessonId, $userId  );

    foreach ( $quizzes as $quiz ) {
        $quizId =   $quiz["post"]->ID;
        $score  +=    get_LD_Converted_QuizScore($quizId, $userId);
    }

    if ( isset($score))   $response->rawScore =$score;

    /***************** TRANSLATE TO FORMULA SCORE ***************************/

    // $items = p2p_type('formulae_to_lessons')->get_adjacent_items( $lesson->post[ID] );


    $p2pTable = $wpdb->prefix . "p2p";
    $query = $wpdb->prepare("SELECT p2p_from FROM {$p2pTable} WHERE p2p_to = %d and p2p_type = %s", $lessonId , 'formulae_to_lessons');
    $scoring_id =$wpdb->get_var($query);

    if ( isset($scoring_id)) {
        $meta_key = 's:' . strval($score);
        $metaTable = $wpdb->prefix . "postmeta";
        $query = $wpdb->prepare("SELECT meta_value FROM {$metaTable} WHERE post_id = %d and meta_key = %s", $scoring_id , $meta_key);
        $convertedScore =$wpdb->get_var($query);
        $response->convertedScore = $convertedScore;
    }

    return $response;

}


function LD_Converted_LessonScore_ShortCode_Handler($attributes) {
    $lesson_id = $attributes['lessonid'];
    return get_LD_Converted_LessonScore($lesson_id)->convertedScore;

}

function LD_Raw_LessonScore_ShortCode_Handler($attributes) {
    $lesson_id = $attributes['lessonid'];
    return get_LD_Converted_LessonScore($lesson_id)->rawScore;

}



function get_LD_Converted_CourseScore($lessons) {


    $response = (object)[];
    $response->totalConvertedScore = 0;
    $response->totalRawScore = 0;



    /********* ADD UP ALL THE LESSON SCORES *****************************/

    foreach ( $lessons as $lesson ) {
       // $lessonScore =  getLessonScore($lesson, $userId) ;
        $lessonId = $lesson["post"]->ID;
        $lessonScore =  get_LD_Converted_LessonScore($lessonId) ;


        if( isset($lessonScore->convertedScore) ) {
            $convertedScore = $lessonScore->convertedScore;
            if( $convertedScore < 100 ) $convertedScore = $convertedScore * 10;
        }

        $response->totalConvertedScore +=  $convertedScore;
        $response->totalRawScore +=  $lessonScore->rawScore;

    }



    return $response;

}

function LD_Converted_CourseScore_ShortCode_Handler($attributes) {
    $course_id = $attributes['courseid'];
    $user_id =  get_current_user_id();
    $lessons = learndash_get_course_lessons_list( $course_id, $user_id );
    return get_LD_Converted_CourseScore($lessons, $user_id)->totalConvertedScore;

}

function LD_Raw_CourseScore_ShortCode_Handler($attributes) {
    $course_id = $attributes['courseid'];
    $user_id =  get_current_user_id();
    $lessons = learndash_get_course_lessons_list( $course_id, $user_id );
    return get_LD_Converted_CourseScore($lessons, $user_id)->totalRawScore;

}

function LD_Raw_QuizScore_ShortCode_Handler($attributes) {
    $quiz_id = $attributes['quizid'];
    return get_LD_Converted_QuizScore($quiz_id);

}

function sayHello( $atts ) {
    return ( '<h1>Hello World!</h1>');
}

add_shortcode( 'ld-converted-course-score' ,'LD_Converted_CourseScore_ShortCode_Handler' );
add_shortcode( 'ld-raw-course-score'       ,'LD_Raw_CourseScore_ShortCode_Handler' );


add_shortcode( 'ld-converted-lesson-score' ,'LD_Converted_LessonScore_ShortCode_Handler' );
add_shortcode( 'ld-raw-lesson-score'       ,'LD_Raw_LessonScore_ShortCode_Handler' );

add_shortcode( 'ld-raw-quiz-score'       ,'LD_Raw_QuizScore_ShortCode_Handler' );





