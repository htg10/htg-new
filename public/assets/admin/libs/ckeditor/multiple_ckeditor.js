         
// The instanceReady event is fired, when an instance of CKEditor has finished
// its initialization.
CKEDITOR.on( 'instanceReady', function( ev ) {
    // Show the editor name and description in the browser status bar.
    document.getElementById( 'eMessage' ).innerHTML = 'Instance <code>' + ev.editor.name + '<\/code> loaded.';

    // Show this sample buttons.
    document.getElementById( 'eButtons' ).style.display = 'block';
});

function InsertHTML() {
    // Get the editor instance that we want to interact with.
    var editor = CKEDITOR.instances.editor1;
    var value = document.getElementById( 'htmlArea' ).value;

    // Check the active editing mode.
    if ( editor.mode == 'wysiwyg' )
    {
        // Insert HTML code.
        // http://docs.ckeditor.com/#!/api/CKEDITOR.editor-method-insertHtml
        editor.insertHtml( value );
    }
    else
        alert( 'You must be in WYSIWYG mode!' );
}

function InsertText() {
    // Get the editor instance that we want to interact with.
    var editor = CKEDITOR.instances.editor1;
    var value = document.getElementById( 'txtArea' ).value;

    // Check the active editing mode.
    if ( editor.mode == 'wysiwyg' )
    {
        // Insert as plain text.
        // http://docs.ckeditor.com/#!/api/CKEDITOR.editor-method-insertText
        editor.insertText( value );
    }
    else
        alert( 'You must be in WYSIWYG mode!' );
}

function SetContents() {
    // Get the editor instance that we want to interact with.
    var editor = CKEDITOR.instances.editor1;
    var value = document.getElementById( 'htmlArea' ).value;

    // Set editor contents (replace current contents).
    // http://docs.ckeditor.com/#!/api/CKEDITOR.editor-method-setData
    editor.setData( value );
}

function GetContents() {
    // Get the editor instance that you want to interact with.
    var editor = CKEDITOR.instances.editor1;

    // Get editor contents
    // http://docs.ckeditor.com/#!/api/CKEDITOR.editor-method-getData
    alert( editor.getData() );
}

function ExecuteCommand( commandName ) {
    // Get the editor instance that we want to interact with.
    var editor = CKEDITOR.instances.editor1;

    // Check the active editing mode.
    if ( editor.mode == 'wysiwyg' )
    {
        // Execute the command.
        // http://docs.ckeditor.com/#!/api/CKEDITOR.editor-method-execCommand
        editor.execCommand( commandName );
    }
    else
        alert( 'You must be in WYSIWYG mode!' );
}

function CheckDirty() {
    // Get the editor instance that we want to interact with.
    var editor = CKEDITOR.instances.editor1;
    // Checks whether the current editor contents present changes when compared
    // to the contents loaded into the editor at startup
    // http://docs.ckeditor.com/#!/api/CKEDITOR.editor-method-checkDirty
    alert( editor.checkDirty() );
}

function ResetDirty() {
    // Get the editor instance that we want to interact with.
    var editor = CKEDITOR.instances.editor1;
    // Resets the "dirty state" of the editor (see CheckDirty())
    // http://docs.ckeditor.com/#!/api/CKEDITOR.editor-method-resetDirty
    editor.resetDirty();
    alert( 'The "IsDirty" status has been reset' );
}

function Focus() {
    CKEDITOR.instances.editor1.focus();
}

function onFocus() {
    document.getElementById( 'eMessage' ).innerHTML = '<b>' + this.name + ' is focused </b>';
}

function onBlur() {
    document.getElementById( 'eMessage' ).innerHTML = this.name + ' lost focus';
}

            // Replace the <textarea id="editor1"> with an CKEditor instance.
            CKEDITOR.replace( 'editor1', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

            //editor2
            CKEDITOR.replace( 'editor2', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

            //editor3
            CKEDITOR.replace( 'editor3', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

            //editor4
            CKEDITOR.replace( 'editor4', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

            //editor5
            CKEDITOR.replace( 'editor5', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

            //editor6
            CKEDITOR.replace( 'editor6', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

            //editor7
            CKEDITOR.replace( 'editor7', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

            //editor8
            CKEDITOR.replace( 'editor8', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

            //editor9
            CKEDITOR.replace( 'editor9', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

            //editor10
            CKEDITOR.replace( 'editor10', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

            //editor11
            CKEDITOR.replace( 'editor11', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

            //editor12
            CKEDITOR.replace( 'editor12', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

            //editor13
            CKEDITOR.replace( 'editor13', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });


                        //editor14
            CKEDITOR.replace( 'editor14', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

                //editor15
            CKEDITOR.replace( 'editor15', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });


                //editor16
            CKEDITOR.replace( 'editor16', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

                //editor17
            CKEDITOR.replace( 'editor17', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

                //editor18
            CKEDITOR.replace( 'editor18', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });


                //editor19
            CKEDITOR.replace( 'editor19', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

                //editor20
            CKEDITOR.replace( 'editor20', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

                   //editor21
            CKEDITOR.replace( 'editor21', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

                   //editor22
            CKEDITOR.replace( 'editor22', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

                   //editor23
            CKEDITOR.replace( 'editor23', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

                   //editor24
            CKEDITOR.replace( 'editor24', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });


       //editor25
            CKEDITOR.replace( 'editor25', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

                   //editor26
            CKEDITOR.replace( 'editor26', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });


       //editor27
            CKEDITOR.replace( 'editor27', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

                   //editor28
            CKEDITOR.replace( 'editor28', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

                   //editor29
            CKEDITOR.replace( 'editor29', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

                   //editor30
            CKEDITOR.replace( 'editor30', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

              //editor201
            CKEDITOR.replace( 'editor201', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

             //editor202
            CKEDITOR.replace( 'editor202', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });
             //editor203
            CKEDITOR.replace( 'editor203', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });
             //editor204
            CKEDITOR.replace( 'editor204', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

             //editor211
            CKEDITOR.replace( 'editor211', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

             //editor212
            CKEDITOR.replace( 'editor212', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });
             //editor213
            CKEDITOR.replace( 'editor213', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

             //editor214
            CKEDITOR.replace( 'editor214', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

             //editor221
            CKEDITOR.replace( 'editor221', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });
              //editor222
            CKEDITOR.replace( 'editor222', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });
              //editor223
            CKEDITOR.replace( 'editor223', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });
              //editor224
            CKEDITOR.replace( 'editor224', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });

              //editor231
            CKEDITOR.replace( 'editor231', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });
            //editor232
            CKEDITOR.replace( 'editor232', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });
             //editor233
            CKEDITOR.replace( 'editor233', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });
             //editor234
            CKEDITOR.replace( 'editor234', {
                on: {
                    focus: onFocus,
                    blur: onBlur,

                    // Check for availability of corresponding plugins.
                    pluginsLoaded: function( evt ) {
                        var doc = CKEDITOR.document, ed = evt.editor;
                        if ( !ed.getCommand( 'bold' ) )
                            doc.getById( 'exec-bold' ).hide();
                        if ( !ed.getCommand( 'link' ) )
                            doc.getById( 'exec-link' ).hide();
                    }
                }
            }, { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });