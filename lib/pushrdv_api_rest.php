<?php

/**
 * @param $customer_id
 * @param $private_key
 * @return Success or error
 */
function checkCustomerAuth($customer_id, $private_key, $base_url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $private_key)));
    curl_setopt($ch, CURLOPT_URL, $base_url.'/customer_rest/authentification/'.$customer_id);
    $result=curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

/**
 * @param $reseller_id
 * @param $private_key
 * @return Success or error
 */
function checkResellerAuth($reseller_id, $private_key, $base_url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $private_key)));
    curl_setopt($ch, CURLOPT_URL, $base_url.'/reseller_rest/authentification/'.$reseller_id);
    $result=curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

function getStageCategories(){
    $auth = isAuth();
    if($auth != false){
        if(checkAuthAction()){
            if(isset($auth['is_reseller']) && $auth['is_reseller'] == 1){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/reseller_rest/get/stage/categories/'.$auth['customer_id']);
                $result=curl_exec($ch);
                curl_close($ch);
            }else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/customer_rest/get/stage/categories/'.$auth['customer_id']);
                $result=curl_exec($ch);
                curl_close($ch);
            }
            return json_decode($result, true);
        }
    }
    return array('error' => 'Erreur d\'authentification');
}

/**
 * @return array of agencys formatted as array
 */
function getAgencys(){
    $auth = isAuth();
    if($auth != false){
        if(checkAuthAction()){
            if(isset($auth['is_reseller']) && $auth['is_reseller'] == 1){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/reseller_rest/get/agencys/'.$auth['customer_id']);
                $result=curl_exec($ch);
                curl_close($ch);
            }else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/customer_rest/get/agencys/'.$auth['customer_id']);
                $result=curl_exec($ch);
                curl_close($ch);
            }
            return json_decode($result, true);
        }
    }
    return array('error' => 'Erreur d\'authentification');
}

function getStages($customer_id, $limit, $stage_category_id){
    $auth = isAuth();
    if($auth != false){
        if(checkAuthAction()){
            if(isset($auth['is_reseller']) && $auth['is_reseller'] == 1){
                if($customer_id == false){
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                    curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/stage_rest/get/reseller/stages/'.$auth['customer_id'].'/'.$limit.'/'.$stage_category_id);
                    $result=curl_exec($ch);
                    curl_close($ch);
                }else{
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                    curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/stage_rest/get/customer/stages/'.$customer_id.'/'.$limit.'/'.$stage_category_id);
                    $result=curl_exec($ch);
                    curl_close($ch);
                }
            }else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/stage_rest/get/customer/stages/'.$auth['customer_id'].'/'.$limit.'/'.$stage_category_id);
                $result=curl_exec($ch);
                curl_close($ch);
            }

            return json_decode($result, true);
        }
    }
    return array('error' => 'Erreur d\'authentification, rendez-vous sur la page PushRDV dans l\'admin de votre site pour vous authentifer sur la plateforme PushRDV');
}

/**
 * GetAgencyStages
 * @param $agency_shortname
 * @return array of stages formatted as array
 */
function getAgencyStages($limit, $agency_shortname, $stage_category_id){
    $auth = isAuth();
    if($auth != false){
        if(checkAuthAction()){
            if(isset($auth['is_reseller']) && $auth['is_reseller'] == 1){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/stage_rest/get/reseller/agency/stages/'.$auth['customer_id'].'/'.$limit.'/'.$agency_shortname.'/'.$stage_category_id);
                $result=curl_exec($ch);
                curl_close($ch);
            }else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/stage_rest/get/agency/stages/'.$auth['customer_id'].'/'.$limit.'/'.$agency_shortname.'/'.$stage_category_id);
                $result=curl_exec($ch);
                curl_close($ch);
            }

            return json_decode($result, true);
        }
    }
    return array('error' => 'Erreur d\'authentification, rendez-vous sur la page PushRDV dans l\'admin de votre site pour vous authentifer sur la plateforme PushRDV');
}

/**
 * @return array of agencys with stages
 */
function getAgencysWithStages(){
    $auth = isAuth();
    if($auth != false){
        if(checkAuthAction()){
            if(isset($auth['is_reseller']) && $auth['is_reseller'] == 1){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/reseller_rest/get/agencys/with/stages/'.$auth['customer_id']);
                $result=curl_exec($ch);
                curl_close($ch);
            }else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/customer_rest/get/agencys/with/stages/'.$auth['customer_id']);
                $result=curl_exec($ch);
                curl_close($ch);
            }
            return json_decode($result, true);
        }
    }
    return array('error' => 'Erreur d\'authentification');
}

function getStageInfos($stage_id){
    $auth = isAuth();
    if($auth != false){
        if(checkAuthAction()){

            if(isset($auth['is_reseller']) && $auth['is_reseller'] == 1){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/stage_rest/get/stage/infos/'.$auth['customer_id'].'/0/'.$stage_id);
                $result=curl_exec($ch);
                curl_close($ch);
            }else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('private_key' => $auth['customer_pkey'])));
                curl_setopt($ch, CURLOPT_URL, $auth['base_url'].'/stage_rest/get/stage/infos/0/'.$auth['customer_id'].'/'.$stage_id);
                $result=curl_exec($ch);
                curl_close($ch);
            }
            return json_decode($result, true);
        }
    }
    return array('error' => 'Erreur d\'authentification');
}