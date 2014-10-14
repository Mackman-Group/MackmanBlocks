(function() {
	tinymce.create('tinymce.plugins.bk_shortcodes', {
		/**
		* Initializes the plugin, this will be executed after the plugin has been created.
		* This call is done before the editor instance has finished it's initialization so use the onInit event
		* of the editor instance to intercept that event.
		*
		* @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		* @param {string} url Absolute URL to where the plugin is located.
		*/
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('bk_shortcode_popup', function(a, args) {
				var popup_id = args.id;
				
				jQuery("#" + popup_id + "_editor").dialog("open");
				
			});
		},

		/**
		* Creates control instances based in the incomming name. This method is normally not
		* needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		* but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		* method can be used to create those.
		*
		* @param {String} n Name of the control to create.
		* @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		* @return {tinymce.ui.Control} New control instance or null if no control was created.
		*/
		createControl: function(n, cm) {
        switch (n) {
            case 'bk_button':
                var c = cm.createSplitButton('bk_button', {
                    title : 'Add Shortcode',
                    image : bkglobal.tinymce_plugin_root_dir + '/images/icon.png',
                    onclick : function() {}
                });

                c.onRenderMenu.add(function(c, m) {
                	 m.add({title : 'Progress Bar', onclick : function() {
                         tinyMCE.activeEditor.execCommand('bk_shortcode_popup', false, {id: 'bk_progress_bar'});
                     }});
                	
                	m.add({title : 'Dropcaps', onclick : function() {
                        tinyMCE.activeEditor.execCommand('bk_shortcode_popup', false, {id: 'bk_dropcap'});
                    }});
                    
                    m.add({title : 'Buttons', onclick : function() {
                        tinyMCE.activeEditor.execCommand('bk_shortcode_popup', false, {id: 'bk_button'});
                    }});
                    
                    m.add({title : 'Big Text', onclick : function() {
                        tinyMCE.activeEditor.execCommand('bk_shortcode_popup', false, {id: 'bk_big_text'});
                    }});
                     
                    m.add({title : 'Social Icons', onclick : function() {
                        tinyMCE.activeEditor.execCommand('bk_shortcode_popup', false, {id: 'bk_social'});
                    }});
                    
                    m.add({title : 'Highlights', onclick : function() {
                        tinyMCE.activeEditor.execCommand('bk_shortcode_popup', false, {id: 'bk_highlight'});
                    }});
                    
                    
                    m.add({title : 'Videos', onclick : function() {
                        tinyMCE.activeEditor.execCommand('bk_shortcode_popup', false, {id: 'bk_video'});
                    }});
                    
                    m.add({title : 'Add Contact Form', onclick : function() {
                        var name = prompt("Please enter button text","Send");
                        tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<p>[bk_contact_form_widget button_text="' + name + '" /]</p>');
                    }});
                    
                    m.add({title : 'Add Regular Tab', onclick : function() {
                        var name = prompt("Please enter tab name","Tab");
                        tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<p>[bk_tab title="' + name + '"]</p><p>PLACE CONTENT FOR TAB HERE</p><p>[/bk_tab]</p>');
                    }});
                    
                    m.add({title : 'Add Accordion Section', onclick : function() {
                        var name = prompt("Please enter section name","Tab");
                        tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<p>[bk_accordion_section title="' + name + '"]</p><p>PLACE CONTENT FOR ACCORDION SECTION HERE</p><p>[/bk_accordion_section]</p>');
                    }});
                });

              // Return the new splitbutton instance
              return c;
        }
        return null;
    },

		/**
		* Returns information about the plugin as a name/value array.
		* The current keys are longname, author, authorurl, infourl and version.
		*
		* @return {Object} Name/value array containing information about the plugin.
		*/
		getInfo : function() {
			return {
				longname : 'BillyKids shortcode generator',
				author : 'BillyKid',
				authorurl : 'http://billykids-lab.net',
				infourl : 'http://billykids-lab.net',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('bk_shortcodes', tinymce.plugins.bk_shortcodes);
})();