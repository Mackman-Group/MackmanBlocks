
(function() {
	tinymce.create('tinymce.plugins.bk_content_composer', {

		init : function(ed, url) {
			var t = this;

			t.url = url;

			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('...');
			ed.addCommand('WP_Gallery', function() {
				var el = ed.selection.getNode(), vp = tinymce.DOM.getViewPort(), W = ( 720 < vp.w ) ? 720 : vp.w;

				if ( el.nodeName != 'IMG' ) return;
				if ( ed.dom.getAttrib(el, 'class').indexOf('wpGallery') == -1 )	return;

				var post_id = tinymce.DOM.get('post_ID').value;
				tb_show('', tinymce.documentBaseURL + '/media-upload.php?post_id='+post_id+'&tab=gallery&TB_iframe=true');

				tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
			});

			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = t._do_gallery(o.content);
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = t._get_gallery(o.content);
			});
		},

		_do_gallery : function(co) {
			return co.replace(/\[one_half\](.*)\[\/one_half\]/g, function(a,b){
				return '<div class="one_half">' + b + '</div>';
			});
		},

		_get_gallery : function(co) {

			function getAttr(s, n) {
				n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
				return n ? tinymce.DOM.decode(n[1]) : '';
			};

			return co.replace(/(?:<p[^>]*>)*(?:<div class="onehalf"[^>]+>)(.*)(?:<\/div [^>]+>)(?:<\/p>)*/g, function(a,im) {
				var cls = getAttr(im, 'class');

				//if ( cls.indexOf('wpGallery') != -1 )
					return '<p>['+tinymce.trim(getAttr(im, 'title'))+']</p>';

				return a;
			});
		},

		getInfo : function() {
			return {
				longname : 'BillyKids Content Composer',
				author : 'BillyKid',
				authorurl : 'http://billykids-lab.net',
				infourl : 'http://billykids-lab.net',
				version : "1.0"
			};
		}
	});

	tinymce.PluginManager.add('bk_content_composer', tinymce.plugins.bk_content_composer);
})();