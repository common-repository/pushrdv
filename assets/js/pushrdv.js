(function() {
    tinymce.create('tinymce.plugins.pushrdv', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            var values_stages = [];
            var values_stages_cats = [];
            var values_meetings = [];
            jQuery(document).ready(function($) {
                var data = {
                    'action': 'pushrdv_get_agency'
                };
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        if(!response.error){
                            if(response.length < 1){
                                values_stages.push({text: 'Aucune agence', value: 0});
                                values_meetings.push({text: 'Aucune agence', value: 0});
                            }else{
                                var first = {text: 'Toutes les agences', value: 'all'};
                                values_stages.push(first);
                                if('reseller' in response[0]){
                                    response.forEach(function (customer, index) {
                                        if(customer.agencys.length > 0){
                                            var customer_obj = {text: '*  Toutes les agences pour '+customer.name, value: 'customer/'+customer.id};
                                            values_stages.push(customer_obj);
                                            customer.agencys.forEach(function(agency, index){
                                                var obj = {text: ' ---> '+agency.name, value: agency.shortName};
                                                var obj2 = {text: agency.name, value: agency.id};
                                                values_stages.push(obj);
                                                values_meetings.push(obj2)
                                            });
                                        }
                                });
                                }else{
                                    response.forEach(function (element, index) {
                                        var obj = {text: element.name, value: element.shortName};
                                        var obj2 = {text: element.name, value: element.id};
                                        values_stages.push(obj);
                                        values_meetings.push(obj2)
                                    });
                                }
                            }
                        }else{
                            values_stages.push({text: 'Aucune agence', value: 0});
                            values_meetings.push({text: 'Aucune agence', value: 0});
                        }
                    }
                });
                var data = {
                    'action': 'pushrdv_get_stage_categories'
                };
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if(!response.error){
                            if(response.length < 1){
                                values_stages_cats.push({text: 'Aucune catégorie', value: 0});
                            }else{
                                var first = {text: 'Toutes les catégories', value: 0};
                                values_stages_cats.push(first);
                                if('reseller' in response[0]){
                                    response.forEach(function (customer, index) {
                                        if(customer.stage_categories.length > 0){
                                            /*var customer_obj = {text: '*  Toutes les catégories pour '+customer.name, value: 'customer/'+customer.id};
                                            values_stages_cats.push(customer_obj);*/
                                            customer.stage_categories.forEach(function(category, index){
                                                var obj = {text: ' ---> '+category.name, value: category.id};
                                                values_stages_cats.push(obj);
                                            });
                                        }
                                    });
                                }else{
                                    response.forEach(function (element, index) {
                                        var obj = {text: element.name, value: element.id};
                                        values_stages_cats.push(obj);
                                    });
                                }
                            }
                        }else{
                            values_stages_cats.push({text: 'Aucune catégorie', value: 0});
                        }
                    }
                });
            });
            ed.addButton('pushrdv', {
                title : 'PushRDV',
                cmd : 'makeStages',
                image : url+'/../img/pushrdv2.png'
            });
            ed.addCommand('makeStages', function() {
                ed.windowManager.open({
                    title: 'PushRDV',
                    body: [
                        {
                            type: 'listbox',
                            name: 'plugin_choice',
                            label: 'Intégration de',
                            values : [
                                { text: 'Rendez-vous', value: 'meeting' },
                                { text: 'Stages', value: 'stage' },
                                { text: 'Carte des agences', value: 'map' },
                                { text: 'Barre de recherche', value: 'search_bar' }
                            ]
                        }
                    ],
                    onsubmit: function(e) {
                        if(e.data.plugin_choice == 'meeting'){
                            ed.windowManager.close();
                            ed.windowManager.open({
                                title: 'Rendez-vous PushRDV',
                                body: [
                                    {
                                        type: 'listbox',
                                        name: 'agency_id',
                                        label: 'Sélectionnez une agence',
                                        values : values_meetings
                                    }
                                ],
                                onsubmit: function(e) {
                                    // Insert content when the window form is submitted
                                    if(e.data.agency_id != ''){
                                        ed.insertContent('[agencyMeetings agency_id="'+e.data.agency_id+'" /]');
                                    }

                                }
                            });
                        }else if(e.data.plugin_choice == 'map'){
                            ed.windowManager.close();
                            ed.windowManager.open({
                                title: 'Carte de France des stages',
                                body: [
                                    {
                                        type: 'checkbox',
                                        name:  'with_stages',
                                        label: 'Afficher les 5 prochains stages',
                                        checked: true
                                    },
                                    {
                                        type: 'checkbox',
                                        name:  'resize_map',
                                        label: 'Redimensionner la carte',
                                        checked: false
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'height',
                                        value: '500px',
                                        label: 'Hauteur (Ex: 500px ou 20%)'
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'width',
                                        value: '100%',
                                        label: 'Largeur (Ex: 100% ou 600px)'
                                    },
                                    {
                                        type: 'checkbox',
                                        name:  'use_colors',
                                        label: 'Utiliser les couleurs personnalisées',
                                        checked: false
                                    },
                                    {
                                        type: 'colorpicker',
                                        name: 'main_color',
                                        value: '#3467B1',
                                        label: 'Sélectionnez la couleur principale'
                                    },
                                    {
                                        type: 'colorpicker',
                                        name: 'background',
                                        value: '#fff',
                                        label: 'Sélectionnez la couleur de fond'
                                    }
                                ],
                                onsubmit: function(e) {
                                    // Insert content when the window form is submitted
                                    var color = '';
                                    var size = '';
                                    var stages = '';

                                    if(e.data.with_stages == false){
                                        stages = 'with_stages="false"';
                                    }
                                    if(e.data.use_colors == true){
                                        color = 'main_color="'+e.data.main_color+'" background="'+e.data.background+'"';
                                    }
                                    if(e.data.resize_map == true){
                                        size = 'height="'+e.data.height+'" width="'+e.data.width+'"';
                                    }
                                    ed.insertContent('[agenciesMap '+color+' '+size+' '+stages+' /]');

                                }
                            });
                        }else if(e.data.plugin_choice == 'search_bar'){
                            ed.windowManager.close();
                            ed.windowManager.open({
                                title: 'Barre de recherche de stages',
                                body: [
                                    {
                                        type: 'checkbox',
                                        name:  'with_location',
                                        label: 'Recherche géographique',
                                        checked: true
                                    },
                                    {
                                        type: 'checkbox',
                                        name:  'with_description',
                                        label: 'Chercher dans la description des stages',
                                        checked: false
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'limit',
                                        label: 'Nombre de stages à afficher',
                                        values : [{text: '5', value: 5},{text: '10', value: 10, selected: true},{text: '15', value: 15},{text: '20', value: 20},{text: '25', value: 25},{text: 'Tous', value: 0}]
                                    }
                                ],
                                onsubmit: function(e) {
                                    // Insert content when the window form is submitted
                                    var stages = '';

                                    if(e.data.with_location == false){
                                        stages += 'with_location="0"';
                                    }
                                    if(e.data.with_description == true){
                                        stages += ' with_description="1"';
                                    }
                                    if(e.data.limit != 10){
                                        stages += ' limit="'+e.data.limit+'"';
                                    }
                                    ed.insertContent('[stageSearchBar '+stages+' /]');

                                }
                            });
                        }else{
                            ed.windowManager.close();
                            ed.windowManager.open({
                                title: 'Stages PushRDV',
                                body: [
                                    {
                                        type: 'listbox',
                                        name: 'agency_shortname',
                                        label: 'Sélectionnez une agence',
                                        values : values_stages
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'stage_category_id',
                                        label: 'Sélectionnez une catégorie de stage',
                                        values : values_stages_cats
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'limit',
                                        label: 'Nombre de stages à afficher',
                                        values : [{text: '5', value: 5},{text: '10', value: 10, selected: true},{text: '15', value: 15},{text: '20', value: 20},{text: '25', value: 25},{text: 'Tous', value: 0}]
                                    },
                                    {
                                        type: 'checkbox',
                                        name:  'remaining_places',
                                        label: 'Afficher les places restantes',
                                        checked: true
                                    },
                                    {
                                        type: 'checkbox',
                                        name:  'use_colors',
                                        label: 'Utiliser les couleurs personnalisées',
                                        checked: false
                                    },
                                    {
                                        type: 'colorpicker',
                                        name: 'main_color',
                                        value: '#3467B1',
                                        label: 'Sélectionnez la couleur principale'
                                    },
                                    {
                                        type: 'colorpicker',
                                        name: 'background',
                                        value: '#fff',
                                        label: 'Sélectionnez la couleur de fond'
                                    }
                                ],
                                onsubmit: function(e) {
                                    // Insert content when the window form is submitted
                                    if(e.data.agency_shortname != ''){
                                        var remaining = '';
                                        if(e.data.remaining_places == true){
                                            remaining = 'remaining_places="true"'
                                        }else{
                                            remaining = 'remaining_places="false"'
                                        };
                                        if(e.data.use_colors == true){
                                            ed.insertContent('[agencyStages agency_shortname="'+e.data.agency_shortname+'" stage_category_id="'+e.data.stage_category_id+'" limit="'+e.data.limit+'" main_color="'+e.data.main_color+'" background="'+e.data.background+'" '+remaining+'/]');
                                        }else{
                                            ed.insertContent('[agencyStages agency_shortname="'+e.data.agency_shortname+'" stage_category_id="'+e.data.stage_category_id+'" limit="'+e.data.limit+'" '+remaining+'/]');
                                        }
                                    }

                                }
                            });
                        }
                    }
                });
            });

        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'PushRDV Buttons',
                author : 'Keole',
                authorurl : 'http://www.keole.net/',
                infourl : 'http://www.keole.net/',
                version : "0.2"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add( 'pushrdv', tinymce.plugins.pushrdv );
})();