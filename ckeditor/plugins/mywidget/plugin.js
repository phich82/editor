CKEDITOR.plugins.add('mywidget', {
    requires: 'widget',
    icons: 'mywidget',
    init: function (editor) {
        // Load css
        editor.addContentsCss(this.path + 'styles/style.css');
        // Register a widget
        editor.widgets.add('mywidget', {
            dialog: 'mywidget',
            // Register a button
            button: 'Create a simple box',
            template:
                '<div class="simplebox">' +
                    '<h2 class="simplebox-title">Title</h2>' +
                    '<div class="simplebox-content"><p>Content...</p></div>' +
                '</div>',
            editables: {
                title: {
                    selector: '.simplebox-title',
                    // Allow tags in content
                    allowedContent: 'br strong em',
                },
                content: {
                    selector: '.simplebox-content',
                    allowedContent: 'p br ul ol li strong em',
                }
            },
            // Rules: element [attributes]{styles}(classes)
            // Allow elements with specfied classes
            allowedContent:
                'div(!simplebox,align-left,align-right,align-center){width};' +
                'div(!simplebox-content); h2(!simplebox-title)',
            // Define minimum HTML
            requiredContent: 'div(simplebox)',

            upcast: function( element ) {
                return element.name == 'div' && element.hasClass('simplebox');
            },

            init: function () {
                // Get width attr of style of widget
                var width = this.element.getStyle('width');
                // Add width property to data
                if (width) {
                    this.setData('width', width);
                }
                // Add align property to data
                if (this.element.hasClass('align-left')) {
                    this.setData('align', 'left');
                }
                if (this.element.hasClass('align-right')) {
                    this.setData('align', 'right');
                }
                if (this.element.hasClass( 'align-center')) {
                    this.setData('align', 'center');
                }
            },
            data: function() {
                // Remove width attr of style of widget if empty
                if (this.data.width == '') {
                    this.element.removeStyle('width');
                } else {
                    this.element.setStyle('width', this.data.width);
                }
                // Remove classes relate to align
                this.element.removeClass('align-left');
                this.element.removeClass('align-right');
                this.element.removeClass('align-center');
                // Add align class
                if (this.data.align) {
                    this.element.addClass('align-' + this.data.align);
                }
            }
        });
        // Add a dialog
        CKEDITOR.dialog.add('mywidget', this.path + 'dialogs/mywidget.js');
    }
});