window.onload = function() {
    document.getElementById("showPersonButton").addEventListener("click", function(){
        document.getElementById("showPersonButton").style.display = "none";
        document.getElementById("hidePersonButton").style.display = "block";
        document.getElementById("personStuff").style.display = "block";
    });
    document.getElementById("hidePersonButton").addEventListener("click", function(){
        document.getElementById("showPersonButton").style.display = "block";
        document.getElementById("hidePersonButton").style.display = "none";
        document.getElementById("personStuff").style.display = "none";
    });
};


