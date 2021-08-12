// Dialog definition
CKEDITOR.dialog.add('mypluginDialog', function (editor) {

    // Dialog definition
    return {
        title: 'Myplugin Properties',
        minWidth: 400,
        minHeight: 200,
        // Presentation layer
        contents: [
            // Tab 1
            {
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [ // Elements of tab (2 fields)
                    {
                        type: 'text',
                        id: 'abbr',
                        label: 'Abbreviation',
                        validate: CKEDITOR.dialog.validate.notEmpty("Abbreviation field cannot be empty."),
                        // Update values of fields on dialog when it opened
                        setup: function (element) { // element: element from editor (<abbr> tag)
                            // Get content of abbr tag from editor, then add it to Abbreviation field
                            this.setValue(element.getText());
                        },
                        // Update values of element on editor when click on OK button on dialog
                        commit: function(element) { // element: element from editor (<abbr> tag)
                            // Get value of Abbreviation field, then add it to <abbr> tag
                            element.setText(this.getValue());
                        }
                    },
                    {
                        type: 'text',
                        id: 'title',
                        label: 'Explanation',
                        validate: CKEDITOR.dialog.validate.notEmpty("Explanation field cannot be empty."),
                        // Update values of fields on dialog when it opened
                        setup: function (element) { // element: element from editor (<abbr> tag)
                            // Get value of title attr of abbr tag from editor, then add it to Explanation field
                            this.setValue(element.getAttribute('title'));
                        },
                        // Update values of element on editor when click on OK button on dialog
                        commit: function(element) { // element: element from editor (<abbr> tag)
                            // Get value of Explanation field, then add it title attr of <abbr> tag
                            element.setAttribute("title", this.getValue());
                        }
                    }
                ]
            }, { // Tab 2
                id: 'tab-advanced',
                label: 'Advanced Settings',
                elements: [ // Elements of tab (1 field)
                    {
                        type: 'text',
                        id: 'id',
                        label: 'Id',
                        // Update values of fields on dialog when it opened
                        setup: function (element) { // element: element from editor (<abbr> tag)
                            // Get value of id attr of abbr tag from editor, then add it to Id field
                            this.setValue(element.getAttribute('id'));
                        },
                        // Update values of element on editor when click on OK button on dialog
                        commit: function (element) { // element: element from editor (<abbr> tag)
                            // Get value of Id field
                            var id = this.getValue();
                            // If id not empty, add id attr to <abbr> tag. Otherwise, remove it
                            if (id) {
                                element.setAttribute('id', id);
                            } else if (!this.insertMode) {
                                element.removeAttribute('id');
                            }
                        }
                    }
                ]
            }
        ],
        // Dialog wndow logic
        onShow: function () { // The editing behavior for a previously inserted element will use the onShow function
            // Get element selected (highlighted or just having the caret inside)
            var selection = editor.getSelection();
            // Get element in which selection starts
            var element = selection.getStartElement();

            if (element) {
                // Get DOM (tag <abbr>) on editor
                element = element.getAscendant('abbr', true);
            }
            // If <abbr> element not exists, create it
            if (!element || element.getName() != 'abbr') {
                element = editor.document.createElement('abbr');
                // add insertMode to dialog
                this.insertMode = true;
            } else {
                this.insertMode = false;
            }

            // For access it in the onOK function (add element to dialog)
            this.element = element;

            // For invoke the setup functions for the element
            if (!this.insertMode) {
                // Dispatch setup event on each element (setup function on element)
                this.setupContent(element);
            }
        },
        // Plugin behavior
        onOk: function () { // clicking the OK button or pressing the Enter key on the keyboard
            var dialog = this,
                abbr = dialog.element;

            // Populate the element with values entered by the user
            // Every parameter that is passed to the commitContent method will also be passed on to the commit functions
            dialog.commitContent(abbr); // dispatch commit function on every elment

            // If not exists, create new
            if (dialog.insertMode) {
                editor.insertElement(abbr);
            }
        },
    };
});