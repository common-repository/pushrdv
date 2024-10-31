<?php
    
    function checkAuthAction(){
        $auth = isAuth();
        if($auth != false){
            if(isset($auth['is_reseller']) && $auth['is_reseller'] == 1){
                $response = checkResellerAuth($auth['customer_id'], $auth['customer_pkey'], $auth['base_url']);
            }else{
                $response = checkCustomerAuth($auth['customer_id'], $auth['customer_pkey'], $auth['base_url']);
            }
            if($response['ok']){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    function isAuth(){
        global $wpdb;
        $auth = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."pushrdv_auth` WHERE 1", ARRAY_A);
        if(!empty($auth)){
            return $auth;
        }else{
            return false;
        }
    }

    function getColors(){
        global $wpdb;
        $auth = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."pushrdv_auth` WHERE 1", ARRAY_A);
        if(!empty($auth)){
            $colors = array();
            if($auth['main_color'] != null && $auth['main_color'] != ''){
                $colors['main_color'] = $auth['main_color'];
            }
            if($auth['background_color'] != null && $auth['background_color'] != ''){
                $colors['background_color'] = $auth['background_color'];
            }
            return (!empty($colors)?$colors:false);
        }
    }

    function addPushRDVQueryVars($aVars) {
        $aVars[] = "stage_id";
        return $aVars;
    }
    add_filter('query_vars', 'addPushRDVQueryVars');

    function PushRDVRewriteRules($aRules){
        $aNewRules = array('stage/([0-9]+)/?$' => 'index.php?pagename=stage&stage_id=$matches[1]');
        $aRules = $aNewRules + $aRules;
        return $aRules;
    }
    add_filter('rewrite_rules_array', 'PushRDVRewriteRules');

    function insertStagePage(){
        $stage_page = get_page_by_title('Stage');
        if(!$stage_page){
            wp_insert_post(array(
                'post_title'    => 'Stage',
                'post_type'     => 'page',
                'post_status'   => 'publish'
            ));
            flush_rewrite_rules(true);
        }
    }
    add_action( 'init', 'insertStagePage' );

    function includeStageTemplate( $template_path ) {
        if ( is_page('Stage') ) {
            $template_path = __DIR__.'/../templates/pushrdv-stage.php';
        }
        return $template_path;
    }
    add_filter( 'template_include', 'includeStageTemplate');