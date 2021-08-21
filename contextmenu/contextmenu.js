function onReady(fn){var d=document;(d.readyState=='loading')?d.addEventListener('DOMContentLoaded',fn):fn();}

onReady(function () {
    (function() {

        "use strict";

        //////////////////////////////////////////////////////////////////////////////
        //
        // H E L P E R    F U N C T I O N S
        //
        //////////////////////////////////////////////////////////////////////////////

        /**
         * Function to check if we clicked inside an element with a particular class
         * name.
         *
         * @param {Object} e The event
         * @param {String} className The class name to check against
         * @return {Boolean}
         */
        function clickInsideElement(e, className) {
            var el = e.srcElement || e.target;
            if (el.classList.contains(className)) {
                return el;
            }
            while (el = el.parentNode) {
                if (el.classList && el.classList.contains(className)) {
                    return el;
                }
            }
            return false;
        }

        /**
         * Get's exact position of event.
         *
         * @param {Object} e The event passed in
         * @return {Object} Returns the x and y position
         */
        function getPosition(e) {
            var posx = 0;
            var posy = 0;

            if (!e) var e = window.event;

            if (e.pageX || e.pageY) {
                posx = e.pageX;
                posy = e.pageY;
            } else if (e.clientX || e.clientY) {
                posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
                posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
            }

            return {
                x: posx,
                y: posy
            }
        }

        //////////////////////////////////////////////////////////////////////////////
        //
        // C O R E    F U N C T I O N S
        //
        //////////////////////////////////////////////////////////////////////////////

        /**
         * Variables.
         */
        var contextMenuClassName = "context-menu";
        var contextMenuItemClassName = "context-menu__item";
        var contextMenuLinkClassName = "context-menu__link";
        var contextMenuActive = "context-menu--active";

        var taskItemClassName = "task";
        var taskItemInContext;

        var clickCoords;
        var clickCoordsX;
        var clickCoordsY;

        var menu = document.querySelector("#context-menu");
        var menuItems = menu.querySelectorAll(".context-menu__item");
        var menuState = 0;
        var menuWidth;
        var menuHeight;
        var menuPosition;
        var menuPositionX;
        var menuPositionY;

        var windowWidth;
        var windowHeight;

        /**
         * Initialise our application's code.
         */
        function init() {
            // Listening click event when click on right-mouse
            contextListener();
            // Listening click event on page
            clickListener();
            // Listening keyboard events
            keyupListener();
            // Listening resize events of window
            resizeListener();
        }

        /**
         * Listens for contextmenu events.
         */
        function contextListener() {
            // When click on right-mouse
            document.addEventListener("contextmenu", function(e) {
                taskItemInContext = clickInsideElement(e, taskItemClassName);
                // If click right-mouse on specified items, show contextmenu
                // Otherwise, closing contextmenu
                if (taskItemInContext) {
                    e.preventDefault();
                    toggleMenuOn();
                    positionMenu(e);
                } else {
                    taskItemInContext = null;
                    toggleMenuOff();
                }
            });
        }

        /**
         * Listens for click events.
         */
        function clickListener() {
            // Process tasks on click on items of contextmenu when it is shown
            document.addEventListener("click", function(e) {
                var clickeElIsLink = clickInsideElement(e, contextMenuLinkClassName);

                if (clickeElIsLink) { // Click on items of contextmenu
                    e.preventDefault();
                    menuItemListener(clickeElIsLink);
                } else { // Click out contextmenu for closing contextmenu
                    var button = e.which || e.button;
                    if (button === 1) {
                        toggleMenuOff();
                    }
                }
            });
        }

        /**
         * Listens for keyup events.
         */
        function keyupListener() {
            window.onkeyup = function(e) {
                let EXIT_KEYBOARD = 27;
                // Closing contextmenu when press ESC key
                if (e.keyCode === EXIT_KEYBOARD) {
                    toggleMenuOff();
                }
            }
        }

        /**
         * Window resize event listener
         */
        function resizeListener() {
            // Closing contextmenu when resizing window
            window.onresize = function(e) {
                toggleMenuOff();
            };
        }

        /**
         * Turns the custom context menu on.
         */
        function toggleMenuOn() {
            if (menuState !== 1) {
                menuState = 1;
                // Add active class for showing contextmenu
                menu.classList.add(contextMenuActive);
            }
        }

        /**
         * Turns the custom context menu off.
         */
        function toggleMenuOff() {
            if (menuState !== 0) {
                menuState = 0;
                // Remove active class for closing contextmenu
                menu.classList.remove(contextMenuActive);
            }
        }

        /**
         * Positions the menu properly.
         *
         * @param {Object} e The event
         */
        function positionMenu(e) {
            clickCoords = getPosition(e);
            clickCoordsX = clickCoords.x;
            clickCoordsY = clickCoords.y;

            menuWidth = menu.offsetWidth + 4;
            menuHeight = menu.offsetHeight + 4;

            windowWidth = window.innerWidth;
            windowHeight = window.innerHeight;

            // If total of menu width and distance from clicked position > window width, then
            // window width - menu width. Otherwise, it is distance from clicked position
            if ((windowWidth - clickCoordsX) < menuWidth) {
                menu.style.left = (windowWidth - menuWidth) + "px";
            } else {
                menu.style.left = clickCoordsX + "px";
            }

            // Same as above
            if ((windowHeight - clickCoordsY) < menuHeight) {
                menu.style.top = (windowHeight - menuHeight) + "px";
            } else {
                menu.style.top = clickCoordsY + "px";
            }
        }

        /**
         * Dummy action function that logs an action when a menu item link is clicked
         *
         * @param {HTMLElement} link The link that was clicked
         */
        function menuItemListener(link) {
            console.log( "Task ID - " + taskItemInContext.getAttribute("data-id") + ", Task action - " + link.getAttribute("data-action"));
            toggleMenuOff();
        }

        /**
         * Run the app.
         */
        init();

    })();

    var ContextMenu = {
        start(config) {

        }
    };
});