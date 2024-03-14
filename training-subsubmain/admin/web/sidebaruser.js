$(document).ready(function(){
    var menu = $(".menu");
    var hamburgur = $(".hamburgur");
    var line1 = $(".line1");
    var line2 = $(".line2");
    var menuOpen;

    function openMenu(){
        menu.css("left", "0px");
        line1.css("background", "#8d8d8d")
        line2.css("background", "#8d8d8d")
        hamburgur.css("z-index", "4")
        menuOpen = true;
    }

    function closeMenu(){
        line1.css("background", "#888888")
        line2.css("background", "#888888")
        menu.css("left", "-35vmax");
        menuOpen = false;
    }

    function toggleMenu(){
        if (menuOpen){
            closeMenu();
        }else{
            openMenu();
        }
    }

    hamburgur.on({
        mouseenter: function(){
            openMenu();
        }
    });
    menu.on({
        mouseleave: function(){
            closeMenu();
        }
    });
    hamburgur.on({
        click: function(){
            toggleMenu();
        }
    });
});