window.onload = () => {
    // Get the modal
    var modal = document.getElementsByClassName('my-modal');
    var modal1 = document.getElementsByClassName('my-modal1')

    // Get the button that opens the modal
    var btn = document.getElementsByClassName("myBtn");


    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close");

    // When the user clicks the button, open the modal 
    btn[0].onclick = function () {
        modal[0].style.visibility = "visible";
        modal[0].style.display = "block";

    }

    btn[1].onclick = function () {
        modal1[0].style.visibility = "visible";
        modal1[0].style.display = "block";
    }
    btn[2].onclick = function () {
        modal[1].style.visibility = "visible";
        modal[1].style.display = "block";
    }
    // When the user clicks on <span> (x), close the modal
    span[0].onclick = function () {
        modal[0].style.visibility = "hidden";
    }

    span[1].onclick = function () {
        modal1[0].style.visibility = "hidden";
    }
    span[2].onclick = function () {
        modal[1].style.visibility = "hidden";
    }
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal[0].style.display = "hidden";            
            modal[1].style.display = "hidden";
            modal[1].style.display = "hidden";
            // modal[1].style.display = "inline";
        }
    }

}