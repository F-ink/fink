function maPosition(position) {
    var infopos = "Position déterminée :\n";
    infopos += "Latitude : "+position.coords.latitude +"\n";
    infopos += "Longitude: "+position.coords.longitude+"\n";
    infopos += "Altitude : "+position.coords.altitude +"\n";
    document.getElementById("infoposition").innerHTML = infopos;
    console.log("infopos");
  };


if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(maPosition);
  } else {
    function erreurPosition(error) {
        var info = "Erreur lors de la géolocalisation : ";
        switch(error.code) {
        case error.TIMEOUT:
            info += "Timeout !";
        break;
        case error.PERMISSION_DENIED:
        info += "Vous n’avez pas donné la permission";
        break;
        case error.POSITION_UNAVAILABLE:
            info += "La position n’a pu être déterminée";
        break;
        case error.UNKNOWN_ERROR:
            info += "Erreur inconnue";
        break;
        }
    }
    document.getElementById("infoposition").innerHTML = info;
  };