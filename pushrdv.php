<?php

    /**
    * Plugin Name: PushRDV
    * Description: Permet l'intégration de la prise de rendez-vous et de la réservation des stages via la plateforme <a href="http://wbd.pushrdv.com/" target="_blank">PushRDV</a>
    * Version: 2.5.2
    * Author: Keole
    * Author URI: www.keole.net
    */

    include 'lib/pushrdv_ajax.php';
    include 'lib/pushrdv_api_rest.php';
    include 'lib/pushrdv_functions.php';
    include 'lib/pushrdv_display_functions.php';
    include 'lib/pushrdv_shortcodes.php';

    register_activation_hook( __FILE__, 'pushrdv_plugin_activation' ); //Activation du plugin
    add_action( 'admin_menu', 'pushrdv_admin_menu' ); //Initialisation de l'admin : Ajout du menu

    /** Création des tables si elles n'existent pas encore. **/
    function pushrdv_plugin_activation(){
        global $wpdb;
        $wpdb->query('
        CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'pushrdv_auth`(
            `customer_id` bigint(20) NOT NULL,
            `customer_pkey` varchar(20) NOT NULL,
            `base_url` varchar(50) NOT NULL,
            `is_reseller` int(1) NOT NULL,
            PRIMARY KEY (`customer_id`))
        ');
    }

    /** Plugin Update **/
    function pushrdv_update_db_check() {
        global $wpdb;
        $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$wpdb->prefix."pushrdv_auth' AND column_name = 'is_reseller'");
        if(empty($row)){
            $wpdb->query("ALTER TABLE ".$wpdb->prefix."pushrdv_auth ADD is_reseller INT(1) NOT NULL DEFAULT 0");
        }
        $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$wpdb->prefix."pushrdv_auth' AND column_name = 'main_color'");
        if(empty($row)){
            $wpdb->query("ALTER TABLE ".$wpdb->prefix."pushrdv_auth ADD main_color VARCHAR(10) NULL DEFAULT '#3467B1'");
        }
        $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$wpdb->prefix."pushrdv_auth' AND column_name = 'background_color'");
        if(empty($row)){
            $wpdb->query("ALTER TABLE ".$wpdb->prefix."pushrdv_auth ADD background_color VARCHAR(10) NULL DEFAULT '#fff'");
        }
    }
    add_action( 'plugins_loaded', 'pushrdv_update_db_check' );


    /** Ajout des boutons à l'éditeur WP **/
    add_action( 'init', 'pushrdv_buttons' );
    function pushrdv_buttons() {
        add_filter( "mce_external_plugins", "pushrdv_add_buttons" );
        add_filter( 'mce_buttons', 'pushrdv_register_buttons' );
    }
    function pushrdv_add_buttons( $plugin_array ) {
        $plugin_array['pushrdv'] = plugin_dir_url(__FILE__)."assets/js/pushrdv.js";
        return $plugin_array;
    }
    function pushrdv_register_buttons( $buttons ) {
        array_push( $buttons, "|",'pushrdv' );
        return $buttons;
    }

    /** Création du menu Organisations **/
    function pushrdv_admin_menu(){
        add_menu_page( 'PushRDV', 'PushRDV', 'manage_options', plugin_dir_path(__FILE__).'templates/pushrdv-admin.php', '', plugin_dir_url(__FILE__)."assets/img/pushrdv.png", 50 );
    }