<div id="agencies-maps" style="<?php if($height != '' && $height != '500px'){echo 'height:'.$height.';';}if($width != '' && $width != '100%'){echo ' width:'.$width.';';} ?>"></div>
<?php if($main_color != '' && $main_color != '#3467B1' && $background != '' && $background != '#fff'){ ?>
    <style type="text/css">
        .agencies-map-marker h4{
            color: <?php echo $main_color ?> !important;
        }
        .marker-stage{
            border: solid 1px <?php echo $main_color ?> !important;
            background-color:  <?php echo $background ?> !important;
            color:  <?php echo $main_color ?> !important;
        }
        .marker-stage-content{
            color:  <?php echo $main_color ?> !important;
        }
        .marker-stage-content:hover{
            background-color: <?php echo $main_color ?> !important;
            color: <?php echo $background ?> !important;
        }
    </style>
<?php } ?>
<script type="application/javascript">
    jQuery(document).ready(function($) {
        var map = initMap();
        var data = {
            'action': 'pushrdv_get_agencys_with_stage'
        };
        $.ajax({
            type: "POST",
            url: "<?php echo admin_url('admin-ajax.php'); ?>",
            data: data,
            dataType: "json",
            success: function (agencies) {
                if(!agencies.error){
                    var infowindow = new google.maps.InfoWindow();
                    var agency_markers = [];
                    var agency_contents = [];
                    var agency_id = 0;
                    $.each(agencies, function( index, agency ) {
                        var url = '';
                        if(agency.url && agency.url != ''){
                            url = '<li><a href="'+agency.url+'">'+agency.url+'</a></li>';
                        }
                        var agency_content_html = '<div class="agencies-map-marker"><h4><a href="'+agency.url+'">'+agency.name+'</a></h4><ul style="list-style: none; padding-left: 5px;"><li>'+agency.street+' '+agency.postal+' '+agency.city+'</li>'+url+'<li><a href="mailto:'+agency.email+'">'+agency.email+'</a></li><li>'+agency.phone+'</li></ul>';
                        <?php if($with_stages != 'false'){ ?>
                            $.each(agency.stages, function(index, stage){
                                var startDate = stage.startDate.split('-');
                                var endDate = stage.endDate.split('-');
                                agency_content_html += '<div class="marker-stage"><a class="marker-stage-content" href="<?php echo get_site_url(); ?>/stage/'+stage.id+'"><div class="marker-stage-title">'+stage.name+' - '+agency.city+'</div><div class="marker-stage-description">Du <b>'+startDate[0]+'/'+startDate[1]+'</b> au  <b>'+endDate[0]+'/'+endDate[1]+'</b> </div></a></div>'
                            });
                        <?php } ?>
                        agency_content_html+='</div>';
                        agency_contents.push(agency_content_html);
                        agency_markers.push(new google.maps.Marker({
                            map: map,
                            position: { lat: parseFloat(agency.latitude), lng: parseFloat(agency.longitude) },
                            icon: "<?php echo plugin_dir_url(__FILE__).'../assets/img/marker.png'?>",
                            animation: google.maps.Animation.DROP,
                            infoWindow:{
                                content: agency_contents[agency_id]
                            }
                        }));
                        (function (agency_marker, agency_content) {
                            google.maps.event.addListener(agency_marker, "click", function (e) {
                                infowindow.setContent(agency_content);
                                infowindow.open(map, agency_marker);
                            });
                        })(agency_markers[agency_id],agency_contents[agency_id]);
                        agency_id++;

                    });
                }
            }
        });
    });
</script>