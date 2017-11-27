(function() {
	tinymce.create('tinymce.plugins.EditorColumns', {
		init : function(ed, url) {

			ed.addButton('ingress', {
				title : 'Ingress',
				image : url + '/../img/tinymce-button-ingress.png',
				onclick : function(){
					ed.execCommand('mceInsertContent', false, '<div class="ingress">\
						<p>Content goes here</p>\
					</div>');
				}
			});

			ed.addButton('twocol', {
				title : 'Två kolumner',
				image : url + '/../img/tinymce-button-two-cols.png',
				onclick : function(){
					ed.execCommand('mceInsertContent', false, '<div class="content-row row">\
						<div class="col-sm-6">\
							<div class="inner">\
								<p>Content goes here</p>\
							</div>\
						</div>\
						<div class="col-sm-6">\
							<div class="inner">\
								<p>Content goes here</p>\
							</div>\
						</div>\
					</div>');
				}
			});

			ed.addButton('twocolleft', {
				title : 'Två kolumner (Vänster)',
				image : url + '/../img/tinymce-button-two-cols-left.png',
				onclick : function(){
					ed.execCommand('mceInsertContent', false, '<div class="content-row row">\
						<div class="col-sm-8">\
							<div class="inner">\
								<p>Content goes here</p>\
							</div>\
						</div>\
						<div class="col-sm-4">\
							<div class="inner">\
								<p>Content goes here</p>\
							</div>\
						</div>\
					</div>');
				}
			});

			ed.addButton('twocolright', {
				title : 'Två kolumner (Höger)',
				image : url + '/../img/tinymce-button-two-cols-right.png',
				onclick : function(){
					ed.execCommand('mceInsertContent', false, '<div class="content-row row">\
						<div class="col-sm-4">\
							<div class="inner">\
								<p>Content goes here</p>\
							</div>\
						</div>\
						<div class="col-sm-8">\
							<div class="inner">\
								<p>Content goes here</p>\
							</div>\
						</div>\
					</div>');
				}
			});

			ed.addButton('threecol', {
				title : 'Tre kolumner',
				image : url + '/../img/tinymce-button-three-cols.png',
				onclick : function(){
					ed.execCommand('mceInsertContent', false, '<div class="content-row row">\
						<div class="col-sm-4">\
							<div class="inner">\
								<p>Content goes here</p>\
							</div>\
						</div>\
						<div class="col-sm-4">\
							<div class="inner">\
								<p>Content goes here</p>\
							</div>\
						</div>\
						<div class="col-sm-4">\
							<div class="inner">\
								<p>Content goes here</p>\
							</div>\
						</div>\
					</div>');
				}
			});

			ed.addButton('outsidecontent', {
				title : 'Infoga content utanför boxar',
				image : url + '/../img/tinymce-button-outsidecontent.png',
				onclick : function(){
					ed.setContent(ed.getContent() + "...")
				}
			});

		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "Editor columns",
				author : 'Christian Wannerstedt',
				authorurl : 'http://www.kloon.se/',
				infourl : 'http://www.kloon.se/',
				version : "2.0"
			};
		}
	});
	tinymce.PluginManager.add('editorcolumns', tinymce.plugins.EditorColumns);
})();
