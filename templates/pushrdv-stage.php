<?php

/**
 * Template Name: PushRDV Stage
 */

global $wp_query;

wp_enqueue_style('pushrdv_style', plugin_dir_url(__FILE__).'../assets/css/pushrdv.css' );
wp_enqueue_style('pushrdv_wysiwg_color', plugin_dir_url(__FILE__).'../assets/css/wysiwyg-color.css' );
wp_enqueue_script('pushrdv_google_maps', 'https://maps.googleapis.com/maps/api/js?libraries=places' );


if(!isset($wp_query->query_vars['stage_id']) || !isset($wp_query->query_vars['stage_id'])){
    wp_redirect( home_url() );
    exit;
}
if(!($stage = getStageInfos($wp_query->query_vars['stage_id'])) || array_key_exists('error', $stage)){
    wp_redirect( home_url() );
    exit;
}
@get_header();
$auth   = isAuth();
$colors = getColors();

date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR.utf8','fra');// OK

$duration = false;
/** Gestion de l'affichage des dates et heures */
if(count($stage['meetings']) > 0) {
    // Si le stage contient des RDV

    //$duration       = 0;  // Durée cumulée de la durée de tous les RDV
    $dateStageStart = 9900000000000;  // Heure réelle du début du stage
    $dateStageEnd   = 0;  // Heure réelle de la fin du stage

    foreach ($stage['meetings'] as $meeting )
    {
        if($meeting['startUnix'] < $dateStageStart)
            $dateStageStart = $meeting['startUnix'];

        if($meeting['endUnix'] > $dateStageEnd)
            $dateStageEnd = $meeting['endUnix'];

        //$duration+= $meeting['endUnix'] - $meeting['startUnix'];
    }

    $dateStageStart = strftime("%A %d %B %Y à %Hh%M",$dateStageStart);
    $dateStageEnd   = strftime("%A %d %B %Y à %Hh%M",$dateStageEnd);


}else{
    // Si le stage ne contient pas des RDV

    $dateStageStart = strftime("%A %d %B %Y",$stage['start_timestamp']);
    $dateStageEnd   = strftime("%A %d %B %Y",$stage['end_timestamp']);

}

$description = (strlen($stage['description'])>=75)?substr($stage['description'], 0, 72).'...':$description = $stage['description'];
$stageStart = date_create_from_format('d-m-Y H:i', $stage['startDate']);
$stageExpired = false;
if($stageStart <= new DateTime()) $stageExpired = true;



?>
    <div id="content">
        <div class="single-stage"  itemscope itemprop="event" itemtype="http://schema.org/Event">
            <meta class="single-stage-meta" itemprop="startDate" content="<?php echo $stage['startDate'];?>">
            <meta class="single-stage-meta" itemprop="endDate" content="<?php echo $stage['endDate'];?>">
            <meta class="single-stage-meta" itemprop="url" content="<?php echo $auth['base_url'].'/stage/log/'.$stage['agency']['id'].'/'.$stage['id'];?>">
            <h1 itemprop="name" style="color: <?php echo $colors['main_color']; ?>"><?php echo $stage['name'];?></h1>
            <div itemprop="location" class="hideItem" itemscope itemtype="http://schema.org/Place">
                <span itemprop="name"><?php echo $stage['agency']['name'];?></span>
                <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                    <span itemprop="streetAddress"><?php echo $stage['agency']['street'];?></span>
                    <span itemprop="postalCode"><?php echo $stage['agency']['postal'];?></span>
                    <span itemprop="addressLocality"><?php echo $stage['agency']['city'];?></span>
                    <span itemprop="addressCountry">FR</span>
                </div>
            </div>
            <div class="single-stage-head">
                <div class="single-stage-map" <?php if($stage['image']){echo 'style="background-color:#fff"';}?>>
                    <?php
                        if($stage['image']){
                            echo '<img src="https://wbd.pushrdv.com/'.$stage['image'].'" alt="'.$stage['agency']['name'].' '.$stage['agency']['city'].'">';
                        }else if($stage['agency']['latitude'] && $stage['agency']['longitude']){
                            echo '<div id="single-stage-map-gmap" data-latitude="'.$stage['agency']['latitude'].'" data-longitude="'.$stage['agency']['longitude'].'"></div>';
                        }
                    ?>
                </div>
                <div class="single-stage-recap">
                    <?php if($stage['category']){?>
                        <div class="single-stage-category floatLeftChild" style="height:30px;">
                            <div>
                                <?php echo '<img class="" src="' . plugins_url( '../assets/img/label.png"', __FILE__ ) . '">';?>
                            </div>
                            <div>
                                <span><?php echo $stage['category']['name']; ?></span>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="single-stage-calendar floatLeftChild" style="<?php if($stage['duration'] &&$stage['duration'] != ''){ echo 'height:60px;';}else{echo 'height:40px;';}?>">
                        <div>
                            <?php echo '<img class="" src="' . plugins_url( '../assets/img/icon_calendar.png"', __FILE__ ) . '">';?>
                        </div>
                        <div>
                            <?php if($dateStageStart == $dateStageEnd){?>
                                <span>Le <?php echo $dateStageEnd;?></span>
                            <?php }else{ ?>
                                <span>Du <?php echo $dateStageStart;?></span><br>
                                <span>Au <?php echo $dateStageEnd;?></span>
                            <?php }?>
                            <?php if($stage['duration'] &&$stage['duration'] != '')
                                echo '<br>Durée : '.$stage['duration'];
                            ?>
                        </div>
                    </div>
                    <div class="single-stage-place floatLeftChild" style="height:40px;">
                        <div>
                            <?php echo '<img class="" src="' . plugins_url( '../assets/img/placeholder.png"', __FILE__ ) . '">';?>
                        </div>
                        <div>
                            <span><?php echo $stage['agency']['name'];?></span><br>
                            <span><?php echo $stage['agency']['street'].' '.$stage['agency']['postal'].' '.$stage['agency']['city'];?></span>
                        </div>
                    </div>
                    <div class="single-stage-phone floatLeftChild" style="height:30px;">
                        <div>
                            <?php echo '<img class="" src="' . plugins_url( '../assets/img/telephone.png"', __FILE__ ) . '">';?>
                        </div>
                        <div>
                            <span><a href="tel:<?php echo $stage['agency']['phone'];?>"><?php echo $stage['agency']['phone'];?></a></span>
                        </div>
                    </div>
                    <div class="single-stage-email floatLeftChild" style="height:30px;">
                        <div>
                            <?php echo '<img class="" src="' . plugins_url( '../assets/img/mail.png"', __FILE__ ) . '">';?>
                        </div>
                        <div>
                            <span><a href="mailto:<?php echo $stage['agency']['email'];?>"><?php echo $stage['agency']['email'];?></a></span>
                        </div>
                    </div>
                </div>
                <div class="single-stage-buy" itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer" >
                    <meta class="single-stage-meta" itemprop="priceCurrency" content="EUR">
                    <?php if($stage['price']> 0){
                        if($stage['totalPrice'] != 0 && $stage['totalPrice'] != $stage['price']){ ?>
                            <span class="single-stage-price" style="color: <?php echo $colors['main_color'];?>;"><span itemprop="price"><?php echo $stage['totalPrice'];?></span> €</span>
                            <span class="single-stage-accompte" style="color: <?php echo $colors['main_color'];?>;">Accompte à régler <?php echo $stage['price'];?> €</span>
                            <meta class="single-stage-meta" itemprop="lowprice" content="<?php echo $stage['totalPrice']; ?>">
                        <?php }else{ ?>
                            <span class="single-stage-price" style="color: <?php echo $colors['main_color'];?>;"><span itemprop="price"><?php echo $stage['price'];?></span> €</span>
                            <meta class="single-stage-meta" itemprop="lowprice" content="<?php echo $stage['price'];?>">
                        <?php }}else{ ?>
                        <span class="single-stage-price" style="color: <?php echo $colors['main_color'];?>;"><span itemprop="price">0</span> €</span>
                        <meta class="single-stage-meta" itemprop="lowprice" content="0">
                    <?php } ?>
                    <?php if($stageExpired): ?>
                        <span class="single-stage-full">
                            Cette session est passée. <br>
                            <a href="#single-stage-next">Voir nos autres sessions</a>
                        </span>
                    <?php elseif($stage['availablePlaces'] > 0): ?>
                        <span class="single-stage-disponibility">
                            Il reste <span itemprop="offerCount" ><?php echo $stage['availablePlaces']; ?></span> places
                        </span>
                        <a class="single-stage-btn-register" itemprop="url" style="background-color: <?php echo $colors['main_color'];?>;color: <?php echo $colors['background_color'];?>" href="<?php echo $auth['base_url'].'/stage/log/'.$stage['agency']['id'].'/'.$stage['id'];?>">Réserver</a>
                    <?php else: ?>
                        <meta class="single-stage-meta" itemprop="offerCount" content="<?php echo $stage['availablePlaces']; ?>">
                        <meta class="single-stage-meta" itemprop="url" content="<?php echo $auth['base_url'].'/stage/log/'.$stage['agency']['id'].'/'.$stage['id'];?>">
                        <span class="single-stage-full">
                            Cette session est complète. <br>
                            <a href="#single-stage-next">Voir nos autres sessions</a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if($stage['description'] && $stage['description'] != ''):;?>
            <div class="single-stage-description" itemprop="description">
                <h2>Description</h2>
                <?php echo $stage['description'];?>
            </div>
            <?php endif; ?>
            <?php if(count($stage['meetings']) > 0):;?>
                <div class="single-stage-program">
                    <h2>Programme</h2>
                    <?php
                    foreach ($stage['meetings'] as $meeting ){
                        $dateMeetingStart = strftime("%A %d %B %Y à %Hh%M",$meeting['startUnix']);
                        $dateMeetingEnd   = strftime("%A %d %B %Y à %Hh%M",$meeting['endUnix']);
                        $duration         = $meeting['endUnix'] - $meeting['startUnix'];
                    ?>
                        <div class="single-stage-meeting">
                            <h4 class="single-stage-meeting-name" style="color:<?php echo $colors['main_color']; ?>;"><?php echo $meeting['name']; ?></h4>
                            <?php if(!$stageExpired):?>
                                <span>Du <?php echo $dateMeetingStart; ?></span><br>
                                <span>Au <?php echo $dateMeetingEnd; ?></span><br>
                            <?php endif; ?>
                            <span>Durée : <?php echo gmdate("G\hi", $duration); ?></span>
                        </div>
                    <?php } ?>
                </div>
            <?php endif;?>
        </div>
        <div class="single-stage-next" id="single-stage-next">
            <h2>Découvrez nos autres stages</h2>
            <?php
            if($stage['category'] && $stage['category']['id']){
                echo doAgencyStages($stage['agency']['shortName'], 10, $stage['category']['id'],$colors['background_color'],$colors['main_color'], 1);
            }else{
                echo doAgencyStages($stage['agency']['shortName'], 10, 0,$colors['background_color'],$colors['main_color'], 1);
            }
            ?>
        </div>
    </div>

    <script type="application/javascript">
        jQuery(document).ready(function($) {
            if(document.getElementById("single-stage-map-gmap") !== null){
                var mapDiv = document.getElementById('single-stage-map-gmap');

                var map_stage = new google.maps.Map(mapDiv, {
                    //Map centrée sur le centre de la france
                    center: {
                        lat: parseFloat(mapDiv.dataset.latitude),
                        lng: parseFloat(mapDiv.dataset.longitude)
                    },
                    zoom: 6,
                    scrollwheel: false
                });
                new google.maps.Marker({
                    map: map_stage,
                    position: { lat: parseFloat(mapDiv.dataset.latitude), lng: parseFloat(mapDiv.dataset.longitude) },
                    icon: "<?php echo plugin_dir_url(__FILE__).'../assets/img/marker.png'?>",
                    animation: google.maps.Animation.DROP
                })
            }
        });
    </script>
<?php get_footer();?>