<?php if($background != '#fff' || $main_color != '#3467B1'){ ?>
    <style media="all" type="text/css">

        .stage-date div, .stage-content:hover div, .stage-content:hover span, .stage-prices div, .stage-prices span{color: <?php echo $background ?> !important;}
        .stage-date, .stage-content:hover, .stage-prices{background-color: <?php echo $main_color ?> !important;}
        .stage-content{background-color: <?php echo $background ?> !important;}
        .stage-content div, .stage-content span{color: <?php echo $main_color ?> !important;}
        .stage-btn{color: <?php echo $main_color ?> !important; background-color: <?php echo $background ?> !important;}
        .stage-btn:hover{color: <?php echo $background ?> !important; background-color: <?php echo $main_color ?> !important;}
    </style>
<?php } ?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<?php if(!array_key_exists('error', $stages)){ ?>
    <?php foreach((array)$stages as $stage){

        $startDate = new \DateTime($stage['startDate']);
        setlocale(LC_TIME, 'fr_FR.UTF-8');
        $startMonth = strftime('%B', mktime(0, 0, 0, intval($startDate->format('m')), intval($startDate->format('d')), intval($startDate->format('Y'))));
        $endDate = new \DateTime($stage['endDate']);

        /* TODO : Passage de la description à un contenu HTML
        if(strlen($stage['description'])>=75){
            $description = substr($stage['description'], 0, 72).'...';
        }else{
            $description = $stage['description'];
        }*/
        $postal = false;
        if($stage['agency']["postal"] && $stage['agency']["postal"] != '')
            $postal = substr($stage['agency']["postal"], 0, 2);
        ?>
        <div class="row">
            <div class="stage col-lg-12 col-md-12 col-sm-12 col-xs-12" itemscope="" itemprop="event" itemtype="http://schema.org/Event">
                <span class="stage-date col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <div class="stage-day"><?php echo $startDate->format("d"); ?></div>
                    <div class="stage-month"><?php echo ucfirst(strftime("%B", mktime(0, 0, 0, intval($startDate->format('m')), intval($startDate->format('d')), intval($startDate->format('Y'))))); ?></div>
                    <div class="stage-year"><?php echo $startDate->format("Y"); ?></div>
                </span>
                <span class="hidden" itemprop="url"><?php echo get_site_url().'/stage/'.$stage["id"]?></span>
                <div class="hidden" itemprop="location" itemscope="" itemtype="http://schema.org/Place">
                    <span itemprop="name"><?php echo $stage['agency']["name"] ?></span>
                    <span itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
                        <span itemprop="streetAddress"><?php echo $stage['agency']["street"]?></span><span itemprop="postalCode"><?php echo $stage['agency']["postal"]?></span><span itemprop="addressLocality"><?php echo $stage['agency']["city"]?></span><span itemprop="addressCountry">FR</span>
                    </span>
                </div>
                <div>
                    <meta itemprop="startDate" content="<?php echo $startDate->format("Y-m-d").'T'.$startDate->format("H:i:s")?>"/>
                    <meta itemprop="endDate" content="<?php echo $endDate->format("Y-m-d").'T'.$endDate->format("H:i:s")?>"/>
                </div>
                <a class="stage-content col-lg-6 col-md-6 col-sm-6 col-xs-6" href="<?php echo get_site_url().'/stage/'.$stage["id"]?>">
                    <div class="stage-title"><span itemprop="name"><?php echo $stage["name"]?></span> - <?php echo $stage['agency']["city"]; if($postal)echo ' ('.$postal.')';?></div>
<!--                    <div class="stage-description hidden-sm hidden-xs" itemprop="description">--><?php //echo $description?><!--</div>-->
                    <div style="height: 15px"></div>
                    <div class="stage-dates">Du <?php echo $startDate->format("d/m/Y")?> au <?php echo $endDate->format("d/m/Y")?> - <?php echo $stage['agency']["name"]?></div>
                </a>
                <div class="stage-prices col-lg-3 col-md-3 col-sm-3 col-xs-3" itemprop="offers" data-line="1" itemscope itemtype="http://schema.org/AggregateOffer">
                    <?php if($stage['price'] > 0){?>
                        <?php if($stage['totalPrice'] != 0 && $stage['totalPrice'] != $stage['price']){ ?>
                            <div class="stage-price"><span itemprop="price"><?php echo $stage['totalPrice']?></span> €</div>
                            <span class="hidden" itemprop="lowprice"><?php echo $stage["totalPrice"]?></span>
                        <?php  }else{ ?>
                            <div class="stage-price"><span itemprop="price"><?php echo $stage['price']?></span> €</div>
                            <span class="hidden" itemprop="lowprice"><?php echo $stage["price"]?></span>
                        <?php } ?>
                        <?php if($stage["availablePlaces"] > 0 && $remaining_places == 'true'){ ?>
                            <div class="stage-month"><span itemprop="offerCount"><?php echo $stage["availablePlaces"]; ?></span> Places restantes</div>
                        <?php } ?>
                    <?php }elseif($stage["availablePlaces"] > 0 && $remaining_places == 'true'){ ?>
                        <div class="stage-price"><span itemprop="offerCount"><?php echo $stage['availablePlaces']?></span></div>
                        <div class="stage-month">Places restantes</div>
                        <span class="hidden" itemprop="lowprice">NA</span>
                    <?php } ?>
                    <span class="hidden" itemprop="priceCurrency">EUR</span>
                    <span class="hidden" itemprop="url"><?php echo get_site_url().'/stage/'.$stage["id"] ?></span>
                    <?php if($stage["availablePlaces"] == 0){ ?>
                        <a href="javascript:;" class="btn stage-btn disabled">Complet</a>
                    <?php }else{ ?>
                        <a class="btn stage-btn" href="<?php echo get_site_url().'/stage/'.$stage["id"]?>">En savoir plus</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
<?php }else{ ?>
    <div style="background-color: white; color: darkred"><strong><?php echo $stages['error'] ?></strong></div>
<?php } ?>
    </div>
</div>
