<?php

    function makeAgencys() {
        $agencys = getAgencys();
        if(!isset($agencys['error'])){
            if(count($agencys) > 0 ){
                $response = '';
                if(isset($agencys[0]['reseller'])){
                    foreach($agencys as $customer){
                        if(count($customer['agencys']) > 0){
                            $response .= '<h4 style="margin-bottom: 5px;">'.$customer['name'].'</h4>';
                            $response .= '<ul style="margin-top: 5px;">';
                            foreach($customer['agencys'] as $agency){
                                $response .= '<li style="list-style-type: circle;margin-left: 20px;">'.$agency['name'].'</li>';
                            }
                            $response .= '</ul>';
                        }
                    }
                }else{
                    $response = '<ul>';
                    foreach($agencys as $agency){
                        $response .= '<li style="list-style-type: circle;margin-left: 20px;">'.$agency['name'].'</li>';
                    }
                    $response .= '</ul>';
                }
                return $response;
            }else{
                return '<strong>Vous n\'avez pas encore créé d\'agence. Rendez vous dans votre interface d\'administration sur la plateforme <a href="https://wbd.pushrdv.com/">PushRDV</a> pour créer votre première agence.</strong>';
            }
        }else{
            return '<strong>'.$agencys['error'].'</strong>';
        }
    }

    function doAgencyStages($agency_shortname, $limit, $stage_category_id, $background, $main_color, $remaining_places) {
        wp_register_style( 'pushrdv_style', plugin_dir_url(__FILE__).'../assets/css/pushrdv.css' );
        wp_enqueue_style('pushrdv_style');
        wp_register_style('pushrdv_bootstrap_style' , plugin_dir_url(__FILE__).'../assets/css/bootstrap.min.css');
        wp_enqueue_style('pushrdv_bootstrap_style');
        wp_enqueue_script('keole_bootstrap_js', plugin_dir_url(__FILE__).'../assets/js/bootstrap.min.js');
        $auth = isAuth();
        if($auth != false){
            $customer_id = explode('/', $agency_shortname);
            if($agency_shortname == 'all'){
                $stages = getStages(false, $limit, $stage_category_id);
            }elseif(count($customer_id)>1 ){
                $stages = getStages($customer_id[1], $limit, $stage_category_id);
            }else{
                $stages = getAgencyStages($limit, $agency_shortname, $stage_category_id);
            }

            ob_start();
            include __DIR__.'/../templates/pushrdv-stages.php';
            return ob_get_clean();
        }
    }

    function doAgencyMeetings($agency_id) {

        $auth = isAuth();
        if($auth != false){
            $agencys = getAgencys();
            $customerHasAgency = false;
            if(count($agencys) > 0){
                if(isset($agencys[0]['reseller'])){
                    foreach($agencys as $customer){
                        foreach($customer['agencys'] as $agency){
                            if($agency['id'] == $agency_id){
                                $customerHasAgency = true;
                            }
                        }
                    }
                }else{
                    foreach($agencys as $agency){
                        if($agency['id'] == $agency_id){
                            $customerHasAgency = true;
                        }
                    }
                }
            }
            if($customerHasAgency){
                $response = '<iframe width="100%" height="650" frameborder="0" scrolling="yes" marginheight="0" marginwidth="0" src="'.$auth['base_url'].'/rdv/'.$agency_id.'" style="opacity: 1; visibility: visible;"></iframe>';
            }else{
                $response = 'Cette agence n\'existe pas ou n\'est pas rattaché à votre compte';
            }
            return $response;
        }
    }

    function doAgencysMap($width, $height, $background, $main_color, $with_stages){
        wp_register_style( 'pushrdv_style', plugin_dir_url(__FILE__).'../assets/css/pushrdv.css' );
        wp_enqueue_style('pushrdv_style');/*
        wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js', array(), null, true);*/
        wp_enqueue_script('pushrdv_agencies_map', plugin_dir_url(__FILE__).'../assets/js/map.js' );
        wp_enqueue_script('pushrdv_google_maps', 'https://maps.googleapis.com/maps/api/js?libraries=places' );

        ob_start();
        include __DIR__.'/../templates/pushrdv-agencies-map.php';
        return ob_get_clean();
    }

    function doStageSearchBar($with_location, $with_description, $limit){
        wp_enqueue_style('pushrdv_datetime_style' , plugin_dir_url(__FILE__).'../assets/css/jquery.datetimepicker.css');
        wp_enqueue_style('pushrdv_bootstrap_style' , plugin_dir_url(__FILE__).'../assets/css/bootstrap.min.css');
        wp_enqueue_style('pushrdv_search_bar_style' , plugin_dir_url(__FILE__).'../assets/css/search-bar.css');
        wp_enqueue_script('pushrdv_datetime_js', plugin_dir_url(__FILE__).'../assets/js/jquery.datetimepicker.js',  array('jquery' ));
        wp_enqueue_script('pushrdv_bootstrap_js', plugin_dir_url(__FILE__).'../assets/js/bootstrap.min.js');
        wp_enqueue_script('pushrdv_search_bar_js', plugin_dir_url(__FILE__).'../assets/js/search-bar.js' );
        wp_enqueue_script('pushrdv_google_maps', 'https://maps.googleapis.com/maps/api/js?libraries=places' );

        ob_start();
        include __DIR__.'/../templates/pushrdv-search-bar.php';
        return ob_get_clean();
    }