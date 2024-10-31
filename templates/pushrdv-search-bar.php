<div class="row" style="padding: 10px 30px 10px 30px;" id="stage_search_container">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="row">
            <div class="<?php if($with_location == 1){echo 'col-lg-6 col-md-6 col-sm-6 col-xs-12';}else{echo 'col-lg-12 col-md-12 col-sm-12 col-xs-12';}?>">
                <div class="input-group">
                    <div class="input-group-addon"><i class="glyphicon glyphicon-search"></i></div>
                    <input class="form-control" id="stage_search" type="text" placeholder="Recherche d'un stage"/>
                </div>
            </div>
            <?php if($with_location == 1){?>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-9" id="stage_search_address_input_bloc">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></div>
                        <input class="form-control" id="stage_location" type="text" placeholder="Adresse, lieu"/>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3" id="stage_search_distance_input_bloc">
                    <div class="input-group">
                        <select class="form-control" id="stage_location_distance">
                            <option value="10">10 km</option>
                            <option value="20">20 km</option>
                            <option value="30" selected="selected">30 km</option>
                            <option value="50">50 km</option>
                            <option value="100">100 km</option>
                            <option value="200">200 km</option>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <!--<div class="col-lg-2 col-md-2 col-sm-2">
                <div class="input-group">
                    <div class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></div>
                    <input class="form-control" id="stage_start" type="text" placeholder="Entre le <?php /*$dateStart = new \DateTime(); echo $dateStart->format('d/m/Y'); */?>"/>
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2">
                <div class="input-group">
                    <div class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></div>
                    <input class="form-control" id="stage_end" type="text" placeholder="Et le <?php /*$dateEnd = clone($dateStart); $dateEnd->add(new \DateInterval('P10D')); echo $dateEnd->format('d/m/Y'); */?>"/>
                </div>
            </div>-->
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <ul class="dropdown-menu" id="stage_search_results" aria-labelledby="stage_search"></ul>
            </div>
        </div>
    </div>
    <input type="hidden" id="pushrdv_limit" value="<?php echo $limit; ?>"/>
    <input type="hidden" id="pushrdv_with_description" value="<?php echo $with_description; ?>"/>
    <input type="hidden" id="pushrdv_with_location" value="<?php echo $with_location; ?>"/>
    <input type="hidden" id="pushrdv_site_url" value="<?php echo get_site_url(); ?>"/>
</div>
<script type="text/javascript">
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<!--<script type='text/javascript' src='https://maps.googleapis.com/maps/api/js?libraries=places'></script>-->