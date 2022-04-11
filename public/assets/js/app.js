

function showHide(n) {
    const id = "details" + n.toString();
    if(document.getElementById(id).style.display == "none"){
        document.getElementById(id).style.display = "block";
    } 
    else {document.getElementById(id).style.display = "none";
    }
}
 