<?php

    add_action( 'wp_ajax_pushrdv_get_stage_categories', 'getStageCategoriesAjaxAction');
    add_action( 'wp_ajax_nopriv_pushrdv_get_stage_categories', 'getStageCategoriesAjaxAction');
    function getStageCategoriesAjaxAction(){
        $response = getStageCategories();
        echo(json_encode($response));
        die;
    }

    add_action( 'wp_ajax_pushrdv_get_agency', 'getAgencyAjaxAction');
    add_action( 'wp_ajax_nopriv_pushrdv_get_agency', 'getAgencyAjaxAction');
    function getAgencyAjaxAction(){
        $response = getAgencys();
        echo(json_encode($response));
        die;
    }

    add_action( 'wp_ajax_pushrdv_get_agencys_with_stage', 'getAgencysWithStagesAjaxAction');
    add_action( 'wp_ajax_nopriv_pushrdv_get_agencys_with_stage', 'getAgencysWithStagesAjaxAction');
    function getAgencysWithStagesAjaxAction(){
        $response = getAgencysWithStages();
        echo(json_encode($response));
        die;
    }

    add_action( 'wp_ajax_pushrdv_get_stages', 'getStagesAjaxAction');
    add_action( 'wp_ajax_nopriv_pushrdv_get_stages', 'getStagesAjaxAction');
    function getStagesAjaxAction(){
        $response = getStages(false, 0,0);
        echo(json_encode($response));
        die;
    }

    add_action( 'wp_ajax_pushrdv_create_authentification', 'createAuthAction' );
    add_action( 'wp_ajax_nopriv_pushrdv_create_authentification', 'createAuthAction' );
    function createAuthAction(){
        global $wpdb;
        if(isset($_POST['private_key']) && isset($_POST['customer_id'])){
            if(isset($_POST['base_url']) && $_POST['base_url'] != ''){
                if(substr_count($_POST['base_url'], 'http://') != 1){
                    $_POST['base_url'] = 'https://'.$_POST['base_url'];
                }
                $_POST['base_url'] = rtrim($_POST['base_url'], '/');
            }else{
                $_POST['base_url'] = 'https://wbd.pushrdv.com';
            }
            if(isset($_POST['is_reseller']) && $_POST['is_reseller'] == 1){
                $response = checkResellerAuth($_POST['customer_id'], $_POST['private_key'], $_POST['base_url']);
                if(isset($response['ok'])){
                    $wpdb->query('INSERT INTO `'.$wpdb->prefix.'pushrdv_auth`(`customer_id`, `customer_pkey`, `base_url`, `is_reseller`) VALUES ('.$_POST['customer_id'].',"'.$_POST['private_key'].'", "'.$response['reseller']['baseUrl'].'", 1)');
                }
            }else{
                $response = checkCustomerAuth($_POST['customer_id'], $_POST['private_key'], $_POST['base_url']);
                if(isset($response['ok'])){
                    $wpdb->query('INSERT INTO `'.$wpdb->prefix.'pushrdv_auth`(`customer_id`, `customer_pkey`, `base_url`, `is_reseller`) VALUES ('.$_POST['customer_id'].',"'.$_POST['private_key'].'", "'.$response['customer']['reseller']['baseUrl'].'", 0)');
                }
            }
            echo(json_encode($response));
            die;
        }
        echo('error');
        die;
    }

    add_action( 'wp_ajax_pushrdv_save_options', 'saveOptions' );
    add_action( 'wp_ajax_nopriv_pushrdv_save_options', 'saveOptions' );
    function saveOptions(){
        global $wpdb;
        $colors = getColors();
        if(isset($_POST['main_color']) && $_POST['main_color'] != $colors['main_color']){
            $wpdb->query('UPDATE `'.$wpdb->prefix.'pushrdv_auth` SET `main_color` = "'.$_POST['main_color'].'" WHERE 1');
        }
        if(isset($_POST['main_color']) && $_POST['main_color'] != $colors['main_color']){
            $wpdb->query('UPDATE `'.$wpdb->prefix.'pushrdv_auth` SET `background_color` = "'.$_POST['background_color'].'" WHERE 1');
        }
        echo json_encode(getColors());
        die;
    }