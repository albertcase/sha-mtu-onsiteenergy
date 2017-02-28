CKEDITOR.plugins.add( 'dropmenuckedit',
    {
        init : function( editor )
        {

            // Register the command.
            editor.addCommand( 'dropmenuckedit',{
                exec : function( editor )
                {
                  var newhtml = CKEDITOR.dom.element.createFromHtml('<div class="dropmenuckedit"><div>title</div><div><div>content</div></div></div>');
                    // editor.insertHtml('<div class="dropmenuckedit"><div>aaaaaa</div><div>bbbbbb</div></div>');
                    editor.insertElement(newhtml);
                }
            });
            // alert('dedephp!');
            // Register the toolbar button.
            editor.ui.addButton( 'dropmenuckedit',
            {
                label : 'dropshow',
                command : 'dropmenuckedit',
                icon: 'images/html_add.png'
            });
            // alert(editor.name);
        },
    });
