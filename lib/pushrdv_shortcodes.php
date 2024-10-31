<?php

    function makeAgencyStages($atts, $content = null) {
        $colors = getColors();
        extract( shortcode_atts( array(
            'agency_shortname' => 0,
            'limit' => 10,
            'stage_category_id' => 0,
            'background' => $colors['background_color'],
            'main_color' => $colors['main_color'],
            'remaining_places' => 'true'
        ), $atts ) );
                
        return doAgencyStages($agency_shortname, $limit, $stage_category_id, $background, $main_color, $remaining_places);
    }
    add_shortcode("agencyStages","makeAgencyStages");
    
    function makeAgencyMeetings($atts, $content = null) {
    
        extract( shortcode_atts( array(
            'agency_id' => 0
        ), $atts ) );
    
        return doAgencyMeetings($agency_id);
    }
    add_shortcode("agencyMeetings","makeAgencyMeetings");

    function makeAgenciesMap($atts, $content = null) {
        $colors = getColors();
        extract( shortcode_atts( array(
            'width' => '100%',
            'height' => '500px',
            'background' => $colors['background_color'],
            'main_color' => $colors['main_color'],
            'with_stages' => 'true'
        ), $atts ) );

        return doAgencysMap($width, $height, $background, $main_color, $with_stages);
    }
    add_shortcode("agenciesMap","makeAgenciesMap");

    function makeStageSearchBar($atts, $content = null) {

        extract( shortcode_atts( array(
            'with_location' => 1,
            'with_description' => 0,
            'limit' => '10'
        ), $atts ) );

        return doStageSearchBar($with_location, $with_description, $limit);
    }
    add_shortcode("stageSearchBar","makeStageSearchBar");