window.onload = () => {

    // Get the modal
    var modal = document.getElementsByClassName('modal');
    
    // Get the button that opens the modal
    var btn = document.getElementsByClassName("btn");
    
    
    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close");
    
    // When the user clicks the button, open the modal 
    btn[0].onclick = function() {
        modal[0].style.visibility = "visible";
    
    }
    
    btn[1].onclick = function() {
        modal[1].style.visibility = "visible";
    }
    // When the user clicks on <span> (x), close the modal
    span[0].onclick = function() {
        modal[0].style.visibility = "hidden";;
    }
    
    span[1].onclick = function() {
        modal[1].style.visibility = "hidden";;
    }
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "hidden";
        }
    }
    
    }