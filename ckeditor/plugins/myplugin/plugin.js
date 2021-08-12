CKEDITOR.plugins.add('myplugin', {
    icons: 'myplugin',
    init: function (editor) {
        // Create editor command
        // editor.addCommand('insertMyPlugin', {
        //     exec: function (editor) {
        //         var now = new Date();
        //         editor.insertHtml('The current date and time is: <em>' + now.toString() + '</em>');
        //     }
        // });
        editor.addCommand('insertMyPlugin', new CKEDITOR.dialogCommand('mypluginDialog'));

        // Creating the Toolbar Button
        editor.ui.addButton('Myplugin', {
            label: 'Insert Myplugin',
            command: 'insertMyPlugin',
            toolbar: 'insert'
        });

        // Add to context menu
        if (editor.contextMenu) {
            // Register command to context menu
            editor.addMenuGroup('mypluginGroup');
            editor.addMenuItem('mypluginItem', {
                label: 'Edit Abbreviation',
                icon: this.path + 'icons/myplugin.png',
                command: 'insertMyPlugin',
                group: 'mypluginGroup'
            });

            // Enable the Abbreviation context menu for each selected <abbr> element
            editor.contextMenu.addListener(function (element) {
                if (element.getAscendant('abbr', true)) {
                    return {mypluginItem: CKEDITOR.TRISTATE_OFF};
                }
            });
        }

        // Load dialog from specified file when this button clicked
        CKEDITOR.dialog.add('mypluginDialog', this.path + 'dialogs/myplugin.js');
    }
});