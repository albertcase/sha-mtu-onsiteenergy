CKEDITOR.plugins.add( 'hrline',
    {
        init : function( editor )
        {

            // Register the command.
            editor.addCommand( 'hrline',{
                exec : function( editor )
                {
                  var newhtml = CKEDITOR.dom.element.createFromHtml('<hr color="#e63f0c" style="width:60%">');
                    // editor.insertHtml('<div class="dropmenuckedit"><div>aaaaaa</div><div>bbbbbb</div></div>');
                    editor.insertElement(newhtml);
                }
            });
            // alert('dedephp!');
            // Register the toolbar button.
            editor.ui.addButton( 'hrline',
            {
                label : 'hrline',
                command : 'hrline',
                icon: 'images/page_red.png'
            });
            // alert(editor.name);
        },
    });
