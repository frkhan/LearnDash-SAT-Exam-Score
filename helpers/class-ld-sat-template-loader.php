<?php

namespace Samadhan\LearnDash\ScoreConverter;

class Template_Loader{
    
    public function get_template_part( $slug, $name = null, $load = true ) {

        do_action( 'ld_sat_get_template_part_' . $slug, $slug, $name );

        // Setup possible parts
        $templates = array();
        if ( isset( $name ) )
            $templates[] = $slug . '-' . $name . '-template.php';
            $templates[] = $slug . '-template.php';

            // Allow template parts to be filtered
            $templates = apply_filters( 'ld_sat_get_template_part', $templates, $slug, $name );

            // Return the part that is found
            return $this->locate_template( $templates, $load, false );
    }
    
    public function locate_template( $template_names, $load = false, $require_once = true ) {
        // No file found yet
        $located = false;

        // Traverse through template files
        foreach ( (array) $template_names as $template_name ) {

            // Continue if template is empty
            if ( empty( $template_name ) )
                continue;

            $template_name = ltrim( $template_name, '/' );

            // Check templates for frontend section
            if ( file_exists( trailingslashit( wpwa_path ) . 'templates/' . $template_name ) ) {
                $located = trailingslashit( wpwa_path ) . 'templates/' . $template_name;
                break;

            // Check templates for admin section
            } 
//            elseif ( file_exists( trailingslashit( wpwa_path ) . 'admin/templates/' . $template_name ) ) {
//                $located = trailingslashit( wpwa_path ) . 'admin/templates/' . $template_name;
//                break;
//            }
        }

        if ( ( true == $load ) && ! empty( $located ) )
            load_template( $located, $require_once );

        return $located;
    }
}

//$template_loader = new Template_Loader();

?>