window.onload = function() {
    document.getElementById("showTableButton").addEventListener("click", function(){
        document.getElementById("showTableButton").style.display = "none";
        document.getElementById("hideTableButton").style.display = "block";
        document.getElementById("memberTable").style.display = "block";
    });

    document.getElementById("hideTableButton").addEventListener("click", function(){
        document.getElementById("hideTableButton").style.display = "none";
        document.getElementById("showTableButton").style.display = "block";
        document.getElementById("memberTable").style.display = "none";
    });

};


