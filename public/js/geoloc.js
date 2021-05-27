let $map = document.querySelector('#map')

class GoogleMap {

    // constructor() {
    //     this.map = null
    //     this.bounds = null
    //     this.textMarker = null
    // }

    /**
     * Charge la carte
     * @param {HTMLElement} element 
     */
    load(element) {
        $script('https://maps.googleapis.com/maps/api/js', () => {
            let center = {lat: -25.363, lng: 131.044};
            let map = new GoogleMap.maps.Map(element, {
                zoom: 4,
                center:center
            });
        })
    }
}

if ($map !==null){
    let map = new GoogleMap()
    map.load($map)
}