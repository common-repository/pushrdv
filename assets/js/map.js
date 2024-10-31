function initMap() {
    var mapDiv = document.getElementById('agencies-maps');

    return new google.maps.Map(mapDiv, {
        //Map centrée sur le centre de la france
        center: {
            lat: parseFloat(46.9350913),
            lng: parseFloat(2.2887767)
        },
        zoom: 6,
        scrollwheel: false
    });
}




