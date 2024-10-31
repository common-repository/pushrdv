<style type="text/css" media="screen">
    .push-btn{padding: 5px; background-color:#3467B1; color: white; font-weight: 600;text-decoration: none;}
    .push-btn:hover{background-color: #30302F; color: white;}
    .pushrdv_options{width: calc(80% - 30px); display: inline-block; float:left; padding: 10px 15px;}
    .pushrdv_agencies{width: calc(20% - 30px); display: inline-block;background-color: #3467B1; color: white; padding: 10px 15px;}
    .pushrdv_form_group{margin-bottom: 20px;}
    .pushrdv_form_group label{display:block; margin-bottom: 5px; color: #3467B1; font-weight: 600;}
    #pushrdv_options_form{margin-bottom: 20px;}
    .pushrdv_options_form_success{display:none; color: forestgreen; margin-bottom: 20px; font-size: 16px;}
</style>
<div class="wrap">
    <a href="http://wbd.pushrdv.com/" target="_blank" style="display: inline-block"><img  style="display: inline-block" src="<?php echo plugin_dir_url(__FILE__) ?>../assets/img/logo.png"></a>
    <div style="display: inline-block; vertical-align: top; margin-left: 20px;">
        <h3 style="color:#3467B1; margin: 0">Votre plateforme de prise de rendez-vous en ligne</h3>
        <p>Pour accéder aux fonctionnalités proposées par ce module vous devez posséder un compte entreprise sur l'application de gestion d'agendas professionnels et de prise de rendez-vous en ligne PushRDV. </p>
        <a href="http://wbd.pushrdv.com/" target="_blank" class="push-btn">Accéder à la plateforme PushRDV</a>
    </div>
</div>
<br>
<hr>
<?php if(checkAuthAction()){ ?>
    <div class="wrap">
        <div class="pushrdv_options">
            <h2>Vos options</h2>
            <span class="pushrdv_options_form_success">Vos options ont bien été enregistrées !</span>
            <form action="javascript:;" id="pushrdv_options_form">
                <?php $colors = getColors(); ?>
                <div class="pushrdv_form_group">
                    <label for="main_color">Couleur principale : </label>
                    <input type="text" name="main_color" id="main_color" placeholder="#3467B1" value="<?php echo $colors['main_color'];  ?>"/>
                </div>
                <div class="pushrdv_form_group">
                    <label for="background_color">Couleur secondaire : </label>
                    <input type="text" name="background_color" id="background_color" placeholder="#FFF" value="<?php echo $colors['background_color']; ?>"/>
                </div>
                <button type="submit" class="push-btn">Enregistrer</button>
            </form>
            <hr>
            <h2>Informations</h2>
            <p>Pour afficher les stages d'une des agences, cliquez sur le bouton <img src="<?php echo plugin_dir_url(__FILE__)?>../assets/img/pushrdv2.png"> présent dans l'éditeur de contenu wordpress afin de paramétrer le <a href="https://openclassrooms.com/courses/propulsez-votre-site-avec-wordpress/les-shortcodes" target="_blank">Shortcode</a> qui va afficher la liste des stages.
                Sélectionnez une agence, personnalisez les couleurs du bloc et générez le shortcode.</p>
            <p><em>En cas de problèmes techniques veuillez nous contacter via l'adresse suivante : <a href="mailto:support@pushrdv.com">support@pushrdv.com</a></em></p>

        </div>
        <div class="pushrdv_agencies">
            <h2 style="color: white;">Vos agences</h2>
            <p>Voici les agences dans lesquelles vous pouvez activer le module de stages / rendez-vous :</p>
            <?php echo makeAgencys(); ?>
        </div>
    </div>
<?php }else{ ?>
    <div class="wrap">
        <h2>Authentification</h2>
        <p><em>Vous pouvez retrouver les informations d\'authentification suivantes dans les "Paramètres" de votre entreprise sur la plateforme <strong>PushRDV</strong> dans l\'onglet "Préférences générales".</em></p>
        <table>
            <tr>
                <td><label for="customer_id"><strong>ID de votre entreprise * :</strong></label></td>
                <td><input type="text" name="customer_id" id="customer_id" required="required" size="5"></td>
            </tr>
            <tr>
                <td><label for="private_key"><strong>Clé privée * :</strong></label></td>
                <td><input type="text" name="private_key" id="private_key" required="required" size="10"></td>
            </tr>
            <tr>
                <td><label for="base_url"><strong>Url de votre plateforme (si différente de http://wbd.pushrdv.com/) :</strong></label></td>
                <td><input type="text" name="base_url" id="base_url" size="25" placeholder="http://wbd.pushrdv.com/"></td>
            </tr>
            <tr>
                <td><label for="base_url"><strong>Compte revendeur :</strong></label></td>
                <td><input type="checkbox" name="is_reseller" id="is_reseller" ></td>
            </tr>
            <tr>
                <td colspan="2"><em style="color: #c40000">(Ne cochez cette case que si vous êtes autorisé à revendre des abonnements PushRDV à des entreprises)</em></td>
            </tr>
        </table>
        <br><br>
        <a href="javascript:;" id="pushrdv_authentification" class="button button-primary button-large">Se connecter</a>
        <div id="authentification_progress" style="display:none;"><img src="<?php echo plugin_dir_url(__FILE__)?>../assets/img/wpspin_light.gif"><em> Authentification en cours... (Cette action peut durer quelques secondes)</em></div>
        <h3 id="authentification_ok" style="color:darkgreen; margin-bottom: 3px; margin-top: 3px; display:none;">Authentification Réussie, vous pouvez maintenant vous servir du plugin.</h3>
        <h3 id="authentification_error" style="color:darkred; margin-bottom: 3px; margin-top: 3px; display:none;"></h3>
    </div>
<?php } ?>
<script type="text/javascript" >
    jQuery(document).ready(function($) {
        $('#pushrdv_authentification').click(function () {
            $('#authentification_error').hide();
            if($('#customer_id').val() != '' && $('#customer_id').val().length <= 5){
                if($('#private_key').val() != '' && $('#private_key').val().length == 10){
                    $('#pushrdv_authentification').hide();
                    $('#authentification_progress').show();
                    var is_reseller = 0;
                    if($('#is_reseller').is(':checked')){
                        is_reseller = 1;
                    }
                    var data = {
                        'action': 'pushrdv_create_authentification',
                        'customer_id': $('#customer_id').val(),
                        'private_key': $('#private_key').val(),
                        'base_url': $('#base_url').val(),
                        'is_reseller': is_reseller
                    };
                    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    $.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: data,
                        dataType: "json",
                        success: function(response){
                            $('#authentification_progress').hide();
                            if(response.ok){
                                window.location.reload(true);
                            }else{
                                $('#authentification_error').html(response.error);
                                $('#authentification_error').show();
                                $('#pushrdv_authentification').show();
                            }
                        }
                    })
                }else{
                    $('#authentification_error').html('Veuillez saisir une clé privée valide.');
                    $('#authentification_error').show();
                    $('#pushrdv_authentification').show();
                }
            }else{
                $('#authentification_error').html('Veuillez saisir un ID valide.');
                $('#authentification_error').show();
            }
        });

        $('#pushrdv_options_form').on('submit', function(e){
            $('.pushrdv_options_form_success').css('display', 'none');
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: 'pushrdv_save_options', main_color: $('#main_color').val(), background_color: $('#background_color').val()},
                dataType: "json",
                success: function(response){
                    $('.pushrdv_options_form_success').css('display', 'block');
                }
            })
        });
    });
</script>