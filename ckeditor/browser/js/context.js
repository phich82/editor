var ContextMenu = {
    start(config) {
        'use strict';

        var configs = {
            selectorTarget: '.context-menu-target',
            selectorContext: '#context-menu',
            menuItemTypeAttrName: 'data-ctx-item-type', // file | text | folder | link
            contextActiveClassName: 'context-menu--active',
            keysTurnOffContextMenu: [],
            types: {
                file: [
                    {
                        id: 'rename',
                        icon: '<i class="fa fa-eye"></i>',
                        label: 'Rename',
                        event: 'click',
                        action: function (elementTarget, event) {}
                    },
                    {
                        id: 'view',
                        icon: '<i class="fa fa-eye"></i>',
                        label: 'View',
                        event: 'click',
                        action: function (elementTarget, event) {}
                    },
                    {
                        id: 'edit',
                        icon: '<i class="fa fa-edit"></i>',
                        label: 'Edit',
                        event: 'click',
                        action: function (elementTarget, event) {}
                    },
                    {
                        id: 'delete',
                        icon: '<i class="fa fa-trash"></i>',
                        label: 'Delete',
                        event: 'click',
                        action: function (elementTarget, event) {}
                    },
                    {
                        id: 'download',
                        icon: '<i class="fa fa-download"></i>',
                        label: 'Download',
                        event: 'click',
                        action: function (elementTarget, event) {}
                    },
                ],
                folder: [
                    {
                        id: 'rename',
                        icon: '',
                        label: 'Rename',
                        event: 'click',
                        action: function (elementTarget, event) {}
                    },
                ],
                link: [
                    {
                        id: 'link',
                        icon: '<i class="fa fa-download"></i>',
                        label: 'Download',
                        event: 'click',
                        action: function (elementTarget, event) {}
                    },
                ],
            },
            actions: {
                // '{type}.{id}': function (elementTarget, event) {}
                // 'file.rename': function (elementTarget, event) {},
            },
        }

        // Mix configs
        if (typeof config === 'object') {
            configs = Object.assign({}, configs, config);
        }

        var menuState   = 0;
        var contextmenu = document.querySelector(configs.selectorContext);
        var contextMenuActiveClass = configs.contextActiveClassName;
        var menuItemTypeAttrName   = configs.menuItemTypeAttrName;
        var contextMenuItemsClassName = 'context-menu__items';
        var contextMenuItemClassName  = 'context-menu__item';
        var contextMenuLinkClassName  = 'context-menu__link';
        var contextMenuLinkDisableClassName  = 'context-menu__link-disable';

        // Waiting until DOM loaded
        function onReady(fn){var d=document;(d.readyState=='loading')?d.addEventListener('DOMContentLoaded',fn):fn();}

        // Initial
        onReady(function() {
            // Append menu items to context menu
            document.querySelectorAll(configs.selectorTarget).forEach(function (element) {
                element.addEventListener('contextmenu', function () {
                    // Menu Item Type
                    let menuItemType = element.getAttribute(menuItemTypeAttrName);
                    // Menu item type is not be declared
                    if (!configs || !configs.types || !configs.types.hasOwnProperty(menuItemType)) {
                        return;
                    }
                    // Check whether we require automatically to click target element to be selected when click mouse right on it
                    if (configs.activations && configs.activations[menuItemType] === true) {
                        element.click();
                    }
                    // Build menu items based its type
                    let itemsConfig = configs.types[menuItemType];
                    let ul = document.createElement('ul');

                    ul.classList.add(contextMenuItemsClassName)

                    itemsConfig.forEach(function(itemConfig) {
                        let li    = document.createElement('li');
                        let a     = document.createElement('a');
                        let icon  = document.createElement('label');
                        let label = document.createElement('span');

                        // Check menu item disabled
                        let itemDisabled = typeof itemConfig.disabled === 'function'
                            ? itemConfig.disabled(element)
                            : false;

                        li.classList.add(contextMenuItemClassName);
                        a.classList.add(contextMenuLinkClassName);

                        icon.innerHTML  = itemConfig.icon  || '';
                        label.innerHTML = '&nbsp;' + (itemConfig.label || '');

                        if (itemDisabled) {
                            a.classList.add(contextMenuLinkDisableClassName);
                        } else {
                            a.classList.remove(contextMenuLinkDisableClassName);
                        }

                        a.appendChild(icon);
                        a.appendChild(label);
                        li.appendChild(a);

                        // Add event to each menu item on context menu
                        !itemDisabled && li.addEventListener(itemConfig.event || 'click', function (e) {
                            let keyAction = menuItemType + '.' + itemConfig.id;
                            if (configs.actions.hasOwnProperty(keyAction)) {
                                let action = configs.actions[keyAction];
                                if (typeof action === 'function') {
                                    action(element, e);
                                    toggleMenuOff();
                                }
                            } else if (typeof itemConfig.action === 'function') {
                                itemConfig.action(element, e);
                                toggleMenuOff();
                            }
                        });
                        // Prevent showing another context menu when click mouse-right on menu item of context menu
                        li.addEventListener('contextmenu', function(e) {
                            e.stopPropagation();
                            e.preventDefault();
                        });
                        // Add menu item to context menu
                        ul.appendChild(li);
                    });

                    // Add menu items to context menu
                    contextmenu.innerHTML = '';
                    contextmenu.appendChild(ul);
                    // Show context menu
                    contextmenu.style.top =  mouseY(window.event) + 'px';
                    contextmenu.style.left = mouseX(window.event) + 'px';
                    toggleMenuOn();
                    // Prevent default action of the event (preventDefault(), and defaultPrevented)
                    window.event.returnValue = false; // false: prevent default action of the event
                    window.event.preventDefault(); // prevent default action of the event
                });
            });

            // Turn context menu off when click on anywhere
            document.addEventListener('click', function (e) {
                // Check whether we click on inside menu item of context menu
                var clickeElIsLink = clickInsideElement(e, contextMenuLinkClassName);
                // Turn context menu off if click on out of context menu
                if (!clickeElIsLink) {
                    var button = e.which || e.button;
                    if (button === 1) {
                        toggleMenuOff();
                    }
                }
            });

            // Turn context menu off when press ESC key
            keyupListener();
        });

        /**
         * Get X position of mouse
         *
         * @param Object e The event
         * @return int|null
         */
        function mouseX(e) {
            if (e.pageX) {
                return e.pageX;
            }
            if (e.clientX) {
                return e.clientX + (document.documentElement.scrollLeft
                    ? document.documentElement.scrollLeft
                    : document.body.scrollLeft);
            }
            return null;
        }

        /**
         * Get Y position of mouse
         *
         * @param Object e The event
         * @return int|null
         */
        function mouseY(e) {
            if (e.pageY) {
                return e.pageY;
            }
            if (e.clientY) {
                return e.clientY + (document.documentElement.scrollTop
                    ? document.documentElement.scrollTop
                    : document.body.scrollTop);
            }
            return null;
        }

        /**
         * Check if we clicked inside an element with a particular class name
         *
         * @param Object e The event
         * @param String className The class name to check against
         * @return Boolean|HTMLElement
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
         * Turns the custom context menu on.
         */
        function toggleMenuOn() {
            if (menuState !== 1) {
                menuState = 1;
                // Add active class for showing contextmenu
                contextmenu.classList.add(contextMenuActiveClass);
            }
        }

        /**
         * Turns the custom context menu off.
         */
        function toggleMenuOff() {
            if (menuState !== 0) {
                menuState = 0;
                // Remove active class for closing contextmenu
                contextmenu.classList.remove(contextMenuActiveClass);
            }
        }

        /**
         * Listens for keyup events.
         */
        function keyupListener() {
            window.onkeyup = function(e) {
                let EXIT_KEYBOARD = 27; // ESC key
                let keys = [EXIT_KEYBOARD];
                let keysMore = configs.keysTurnOffContextMenu || null;

                // Add more keyboards for turn context menu off
                if (Array.isArray(keysMore) && keysMore.length > 0) {
                    keys = keys.concat(keysMore.filter(function(key) {
                        return keys.indexOf(key) === -1;
                    }));
                }

                // Closing contextmenu when press ESC key
                if (keys.indexOf(e.keyCode) !== -1) {
                    toggleMenuOff();
                }
            }
        }
    },
};
