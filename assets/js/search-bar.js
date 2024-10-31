(function($) {

/****************************** DISPLAY FUNCTIONS ******************************/
    /**
     * Affichage des résultats une fois la recherche effectuée
     */
    function displaySearchResult(stages){
        $('#stage_search_results').show();

        var tab_mois=new Array("Janv.", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Sept.", "Oct.", "Nov.", "Déc.");

        var content = '';
        if(stages.length > 0){

            $.each(stages, function(index, stage){

                var start = new Date(stage.start_timestamp*1000);
                var day = (start.getDate()<10)?'0'+start.getDate():''+start.getDate();
                start = day + ' ' + tab_mois[start.getMonth()];
                
                /*
                var end = new Date(stage.start_timestamp*1000);
                day = (end.getDate()<10)?'0'+end.getDate():''+end.getDate();
                month = ((end.getMonth()+1)<10)?'0'+(end.getMonth()+1):''+(end.getMonth()+1);
                end = day+'/'+month+'/'+end.getFullYear();
                */

                var postalCode = stage.agency.postal.toString();
                var place = stage.agency.city + ' (' + postalCode.substr(0,2) + ') ';

                var spanCategory = '';

                if(stage.category)
                    spanCategory = '<span class="stage_result_category"><b>'+stage.category.name+'</b></span>';

                var description = '';
                if(stage.description != '' && stage.description != null && $('#pushrdv_with_description').val() == 1){
                    if(stage.description.length < ($('#stage_search_results').width()/7.2)){
                        description = '<span class="stage_result_description">'+stage.description+'</span>';
                    }else{
                        description = stage.description.substr(0, $('#stage_search_results').width()/7.2);
                        description = '<span class="stage_result_description">'+description.substr(0, Math.min(description.length, description.lastIndexOf(" ")))+'...</span>'
                    }
                }
                content += '<li class="stage_result"><a href="'+$('#pushrdv_site_url').val()+'/stage/'+stage.id+'"><span class="stage_result_dates"><b>'+start+'</b></span>'+spanCategory+'<span class="stage_result_name"><b>'+stage.name+'</b></span><span class="stage_result_place">' + place + '</span>'+description+'</a></li>';
            });
        }else{
            content = '<li><a href="javascript:;">Aucun stage ne correspond à vos critères</a></li>';
        }
        $('#stage_search_results').html(content);
    }

    /**
     * Cacher les résultats quand tous les critères sont à leurs valeurs par défauts
     */
    function hideSearchResult(stages, location, start, end){
        var searchDefault = false;
        if($('#stage_search').val() == '' || $('#stage_search').val().length < 3){
            searchDefault = true;
        }
        if(searchDefault && location == false && start == false && end == false){
            $('#stage_search_results').hide();
        }else{
            searchStage(stages, location, start, end)
        }
    }

/****************************** UTILITY FUNCTIONS ******************************/
    /**
     * Calcul de la distance entre 2 points
     */
    function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {

        var R = 6371; // Radius of the earth in km
        var dLat = (lat2-lat1)*Math.PI/180;  // deg2rad below
        var dLon = (lon2-lon1)*Math.PI/180;
        var a =
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) *
                Math.sin(dLon/2) * Math.sin(dLon/2)
            ;
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        var d = R * c; // Distance in km
        return d;
    }

/****************************** SEARCH FUNCTIONS ******************************/
    /**
     * Recherche par termes dans un stage (nom, description, category, agency...)
     */
    function searchStageByTerms(stage, search){
        var name = (stage.name != '' && stage.name != null)?stage.name.toUpperCase():'';
        var description = (stage.description != '' && stage.description != null)?stage.description.toUpperCase():'';
        var cat_name = '';
        var cat_description = '';
        var agency = stage.agency.name.toUpperCase()+' '+stage.agency.city.toUpperCase();
        if(stage.category != false){
            cat_name = stage.category.name.toUpperCase();
            cat_description = stage.category.description.toUpperCase();
        }

        var searchMatch = true;
        if(search != false){
            var terms = search.split(' ');
            $.each(terms, function(index, term){
                var foundInName = false;
                var foundInDescription = false;
                var foundInCategory = false;
                var foundInAgency = false;

                if(name.indexOf(term.toUpperCase()) != -1){
                    foundInName = true;
                }
                if(description.indexOf(term.toUpperCase()) != -1){
                    foundInDescription = true;
                }
                if(cat_name.indexOf(term.toUpperCase()) != -1 || cat_description.indexOf(term.toUpperCase()) != -1){
                    foundInCategory = true;
                }
                if(agency.indexOf(term.toUpperCase()) != -1){
                    foundInAgency = true;
                }
                if($('#pushrdv_with_description').val() == 0){
                    foundInDescription = false;
                }
                if(!foundInName && !foundInDescription && !foundInCategory && !foundInAgency){
                    searchMatch = false;
                }
            });
        }
        return searchMatch;
    }

    /**
     * Recherche de stage en fonction distance
     */
    function locationSearch(stage, location, distance){
        var locationMatch = true;
        if(location != false && location.geometry){
            var distanceStage = getDistanceFromLatLonInKm(location.geometry.location.lat(), location.geometry.location.lng(), stage.agency.latitude, stage.agency.longitude)
            if(distanceStage > distance){
                locationMatch = false;
            }
        }
        return locationMatch;
    }

    /**
     * Recherche d'un stage par dates
     */
    function dateSearch(stage, start, end){
        var startMatch = true;
        var endMatch = true;

        return startMatch && endMatch;
    }

    /**
     * Recherche de stage par termes de recherches, lieu, dates
     */
    function searchStage(stages, location, start, end){

        var results = [];

        $.each(stages, function(index, stage){

            if(searchStageByTerms(stage, $('#stage_search').val()) && locationSearch(stage, location, $('#stage_location_distance').val()) && dateSearch(stage, start, end)){
                if(results.length < $('#pushrdv_limit').val()){
                    results.push(stage);
                }
            }
        });
        displaySearchResult(results);
        return results;
    }

    /**
     * Initialisation de la barre de recherche une fois qu'on a récupéré les stages
     */
    function initSearch(stages){
        var location = false;
        var start = false;
        var end = false;

        //INPUTS FOR SEARCH FIELD
        $('#stage_search').on('keyup',function(){
            if($(this).val().length >= 3){
                //Cas valide -> Recherche
                searchStage(stages, location, start, end);
            }else{
                hideSearchResult(stages,location, start, end);
            }
        });

        $('#stage_search_container').mouseleave(function(){
            $('#stage_search_results').hide();
        });
        $('#stage_search').mouseover(function(){
            if($(this).val().length >= 3 || location != false){
                $('#stage_search_results').show();
            }
        });


        //INPUTS FOR LOCATION
        if($('#pushrdv_with_location').val() == 1){

            autocomplete = new google.maps.places.Autocomplete((document.getElementById('stage_location')));
            autocomplete.addListener('place_changed', function(){
                //Cas valide -> Recherche
                location = autocomplete.getPlace();
                if(location.formatted_address == "France" || !location.geometry.location.lat() || !location.geometry.location.lng()){
                    location = false;
                    hideSearchResult(stages,location, start, end);
                }
                searchStage(stages, location, start, end);
            });
            $('#stage_location').on('change',function(){
                if($(this).val() == ''){
                    location = false;
                    hideSearchResult(stages,location, start, end);
                }
            });

            $('#stage_location_distance').on('change',function(){
                if(location != false){
                    searchStage(stages, location, start, end);
                }
            });
            $('#stage_location').mouseover(function(){
                if($('#stage_search').val().length >= 3 || location != false){
                    $('#stage_search_results').show();
                }
            });
            $('#stage_location_distance').mouseover(function(){
                if($('#stage_search').val().length >= 3 || location != false){
                    $('#stage_search_results').show();
                }
            });

        }

        /*//Date start
        var minDate = new Date();
        $('#stage_start').datetimepicker({
            lang: 'fr',
            timepicker: false,
            format: 'd/m/Y',
            autoClose: true,
            defaultDate: new Date(),
            minDate: minDate,
            onSelectDate:function(ct){
                if(ct.dateFormat('d/m/Y') == minDate.dateFormat('d/m/Y')){
                    if(ct.getTime() <= minDate.getTime()){
                        $('#stage_start').val(minDate.dateFormat('d/m/Y'));
                        hideSearchResult(location, start, end);
                    }else{
                        $('#stage_end').datetimepicker({defaultDate: ct, minDate: ct});
                        //Cas valide -> Recherche
                        start = ct;
                        searchStage(stages, location, start, end);
                    }
                }else{
                    $('#stage_end').datetimepicker({defaultDate: ct, minDate: ct});
                    //Cas valide -> Recherche
                    start = ct;
                    searchStage(stages, location, start, end);
                }
            }
        });


        //Date end
        $('#stage_end').datetimepicker({
            lang: 'fr',
            timepicker: false,
            format: 'd/m/Y',
            autoClose: true,
            defaultDate: new Date(),
            minDate: minDate,
            onSelectDate:function(ct){
                if(ct.dateFormat('d/m/Y') == start.dateFormat('d/m/Y')){
                    if(ct.getTime() <= minDate.getTime()){
                        $('#stage_end').val(minDate.dateFormat('d/m/Y'));
                        hideSearchResult(location, start, end);
                    }else{
                        //Cas valide -> Recherche
                        end = ct;
                        searchStage(stages, location, start, end);
                    }
                }else{
                    //Cas valide -> Recherche
                    end = ct;
                    searchStage(stages, location, start, end);
                }
            }
        });*/
    }



/****************************** INIT FUNCTION ******************************/
    $(document).ready(function(){
        /*if (typeof localStorage != 'undefined') {
            if (localStorage.getItem("pushrdv_stages") === null) {*/
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {action: 'pushrdv_get_stages'},
                    dataType: "json",
                    success: function (stages) {
                        //localStorage.setItem("pushrdv_stages", JSON.stringify(stages));
                        initSearch(stages);
                    }
                });
            /*}else{
                initSearch(JSON.parse(localStorage.getItem("pushrdv_stages")));
            }
        }*/
    });


})(jQuery);