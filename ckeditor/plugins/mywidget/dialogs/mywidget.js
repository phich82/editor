CKEDITOR.dialog.add('mywidget', function (editor) {
    // Define elements of dialog window
    return {
        title: 'Edit Simple Box',
        minWidth: 200,
        minHeight: 100,
        contents: [
            {
                id: 'info',
                elements: [
                    // Dialog window UI elements
                    {
                        id: 'align',
                        type: 'select',
                        label: 'Align',
                        items: [
                            [editor.lang.common.notSet, ''],
                            [editor.lang.common.alignLeft, 'left'],
                            [editor.lang.common.alignRight, 'right'],
                            [editor.lang.common.alignCenter, 'center'],
                        ],
                        // Invoked when opening widget
                        // Get data from widget and set them to fields on dialog
                        setup: function (widget) {
                            this.setValue(widget.data.align);
                        },
                        // Update data in widget when data in dialog changed after click on OK button
                        commit: function (widget) {
                            widget.setData('align', this.getValue());
                        }
                    },
                    {
                        id: 'width',
                        type: 'text',
                        label: 'Width',
                        width: '50px',
                        // Invoked when opening widget
                        // Get data from widget and set them to fields on dialog
                        setup: function (widget) {
                            this.setValue(widget.data.width);
                        },
                        // Update data in widget when data in dialog changed after click on OK button
                        commit: function (widget) {
                            widget.setData('width', this.getValue());
                        }
                    }
                ]
            }
        ]
    };
});