<?php
/*
  $Id$

  Designed for: osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Surfalot CMS
  Copyright (c) 2017 Todd Holforty - mtholforty (at) surfalot (at) com

  Released under the GNU General Public License
*/

if ( $_GET['action'] == 'new_content' && $_GET['wysiwyg'] == 'true') { 

// https://www.tinymce.com/download/custom-builds/   
/* <script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>  */

if ( file_exists('../ext/tinymce/tinymce.min.js') ) {
  echo '<script src="../ext/tinymce/tinymce.min.js"></script>';	
} else {
  echo '<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>';	
}
?>

<script>
$(document).ready(function(){

  if (typeof tinymce !== 'undefined') {

//
//    tinymce.PluginManager.add('example', function(editor, url) {
//  
//      // Add a button that opens a window, add: "example" to toolbar
//      editor.addButton('example', {
//          text: 'FA',
//          icon: false,
//          onclick: function() {
//              // Open window
//              editor.windowManager.open({
//                  title: 'Enter Font Awesome Icon Code',
//                  body: [
//                      {type: 'textbox', name: 'icon_code', label: 'Icon Code'}
//                  ],
//                  onsubmit: function(e) {
//                      // Insert content when the window form is submitted
//                      editor.insertContent('<i class="fa ' + e.data.icon_code + '"><!-- i --></i>');
//                  }
//              });
//          }
//      });
//  
//      // Adds a menu item to the tools menu
//      editor.addMenuItem('example', {
//          text: 'Example plugin',
//          context: 'tools',
//          onclick: function() {
//              // Open window with a specific url
//              editor.windowManager.open({
//                  title: 'TinyMCE site',
//                  url: 'http://www.tinymce.com',
//                  width: 400,
//                  height: 300,
//                  buttons: [{
//                      text: 'Close',
//                      onclick: 'close'
//                  }]
//              });
//          }
//      });
//
//    });
//

    tinymce.init({
      selector:'.htmleditor',
      width: '800',
      height: '250',
      plugins: [
        "advlist anchor autolink charmap code colorpicker fullscreen hr",
        "image imagetools link lists media nonbreaking paste searchreplace",
        "table textcolor visualblocks visualchars wordcount"
		/*  */
        /* autoresize autosave bbcode codesample contextmenu 
		   directionality emoticons fullpage importcss insertdatetime 
		   legacyoutput noneditable pagebreak print preview save 
		   spellchecker tabfocus template textpattern */
      ],
	  toolbar1: "undo redo | cut copy paste pastetext | searchreplace | anchor link unlink | blockquote hr nonbreaking charmap | table | image media",
	  toolbar2: "styleselect | bold italic underline strikethrough subscript superscript | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
      toolbar3: "osccustom | forecolor backcolor | fontselect | fontsizeselect | visualblocks visualchars preview | removeformat | fullscreen code",
                 /* formatselect spellchecker */
      /*menubar: "edit insert view format table tools",*/
      menubar: false,
      contextmenu: false,
      /*statusbar: false,*/
      document_base_url: "<?php echo HTTP_SERVER.DIR_WS_CATALOG; ?>",
      content_css: ['../ext/bootstrap/css/bootstrap.min.css', 
                    'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', 
                    '../custom.css', 
                    '../user.css?' + new Date().getTime()
      ],
      relative_urls: false,
      convert_urls: false,
      remove_script_host : false,
      keep_styles: false,
      element_format: "html",
      entity_encoding: 'raw',
      /*verify_html : false,*/
      /*cleanup: false, */
      /* paste_as_text: true, */ /* forces fulltime paste as text */
      style_formats: [
        {title: 'H1', block: 'h1'},
        {title: 'H2', block: 'h2'},
        {title: 'H3', block: 'h3'},
        {title: 'H4', block: 'h4'},
        {title: 'DIV', block: 'div'},
       ],
      entities: "38,amp,34,quot,162,cent,8364,euro,163,pound,165,yen,169,copy,174,reg,8482,trade",
      extended_valid_elements: "i[class]", /* using - in front of an element will strip the tags if empty, using a # will add a non-breaking space into an empty element to preserve it. I decided to use the hack of adding a comment in the empty tag to prevent empty Font Awesome tags it from being stripped in TinyMCE i.e. <i class="fa fa-home"><!-- i --></i> https://core.trac.wordpress.org/ticket/28940 */

	  setup: function(editor) {
		editor.addButton('osccustom', {
		  type: 'menubutton',
		  text: 'osC Custom',
		  icon: false,
		  menu: [{
			text: 'Font Awesome Icon',
			onclick: function() {
              // Open window
              editor.windowManager.open({
                  title: 'Enter Font Awesome Icon Code',
                  body: [
				      {type: 'container', name: 'container', label: 'Example: fa-home', html: 'Visit <a href="http://fontawesome.io/icons/">Font Awesome</a> for samples.'},
                      {type: 'textbox', name: 'iconcode', label: 'Font Awesome Code', value: "fa-"}
                  ],
                  onsubmit: function(e) {
                      // Insert content when the window form is submitted
                      editor.insertContent('<div style="display:inline"><i class="fa ' + e.data.iconcode + '"><!-- i --></i></div>');
                  }
              });
			}
		  }, {
			text: 'Page: Heading',
			onclick: function() {
              // Open window
              editor.windowManager.open({
                  title: 'Enter Your Page Heading',
                  body: [
                      {type: 'textbox', name: 'headingtext', label: 'Heading'}
                  ],
                  onsubmit: function(e) {
                      // Insert content when the window form is submitted
                      editor.insertContent('<div class="page-header"><h1>' + e.data.headingtext + '</h1></div>');
                  }
              });
			}
		  }, {
			text: 'Page: Full Page BS Block',
			onclick: function() {
			  editor.insertContent('<div class="row"><div class="col-sm-12">&nbsp;</div></div>');
			}
		  }, {
			text: 'Page: Half Page BS Blocks',
			onclick: function() {
			  editor.insertContent('<div class="row"><div class="col-sm-6">&nbsp;</div><div class="col-sm-6">&nbsp;</div></div>');
			}
		  }, {
			text: 'Page: Third Page BS Blocks',
			onclick: function() {
			  editor.insertContent('<div class="row"><div class="col-sm-4">&nbsp;</div><div class="col-sm-4">&nbsp;</div><div class="col-sm-4">&nbsp;</div></div>');
			}
		  }, {
			text: 'Page: Quarter Page BS Blocks',
			onclick: function() {
			  editor.insertContent('<div class="row"><div class="col-sm-3">&nbsp;</div><div class="col-sm-3">&nbsp;</div><div class="col-sm-3">&nbsp;</div><div class="col-sm-3">&nbsp;</div></div>');
			}
		  }, {
			text: 'Page: Thumbnail Box w/Border',
			onclick: function() {
			  editor.insertContent('<div class="col-sm-3"><div class="thumbnail equal-height"><div class="caption"><p class="text-center">&nbsp;</p></div></div></div>');
			}
		  }, {
			text: 'Sidebar: Box with Header',
			onclick: function() {
              // Open window
              editor.windowManager.open({
                  title: 'Enter Box Info',
                  body: [
                      {type: 'textbox', name: 'headingtext', label: 'Heading'},
					  {type: 'textbox', name: 'bodytext', label: 'Body'}
                  ],
                  onsubmit: function(e) {
                      // Insert content when the window form is submitted
                      editor.insertContent('<div class="panel panel-default"><div class="panel-heading">' + e.data.headingtext + '</div><div class="panel-body">' + e.data.bodytext + '</div></div>');
                  }
              });
			}
		  }, {
			text: 'Navbar Text Block',
			onclick: function() {
              // Open window
              editor.windowManager.open({
                  title: 'Navbar Text Block Info',
                  body: [
				      {type: 'container', name: 'container', label: 'Example: fa-home', html: 'Visit <a href="http://fontawesome.io/icons/">Font Awesome</a> for samples.'},
                      {type: 'textbox', name: 'iconcode', label: 'Font Awesome Code', value: "fa-"},
					  {type: 'textbox', name: 'linktext', label: 'Text'}
                  ],
                  onsubmit: function(e) {
                      // Insert content when the window form is submitted
                      editor.insertContent('<ul class="nav navbar-nav"><li><p class="navbar-text"><i class="fa ' + e.data.iconcode + '"></i> ' + e.data.linktext + '</p></li></ul>');
                  }
              });
			}
		  }, {
			text: 'Navbar Link Block',
			onclick: function() {
              // Open window
              editor.windowManager.open({
                  title: 'Navbar Text Block Info',
                  body: [
				      {type: 'container', name: 'container', label: 'Example: fa-home', html: 'Visit <a href="http://fontawesome.io/icons/">Font Awesome</a> for samples.'},
                      {type: 'textbox', name: 'iconcode', label: 'Font Awesome Code', value: "fa-"},
					  {type: 'textbox', name: 'urltext', label: 'URL'},
					  {type: 'textbox', name: 'linktext', label: 'Link Text'}
                  ],
                  onsubmit: function(e) {
                      // Insert content when the window form is submitted
                      editor.insertContent('<ul class="nav navbar-nav"><li><a href="' + e.data.urltext + '"><i class="fa ' + e.data.iconcode + '"></i><span class="hidden-sm"> ' + e.data.linktext + '</span></a></li></ul>');
                  }
              });
			}
		  }]
		});
	  }

/*
       style_formats: [
            {title: 'Bold text', inline: 'b'},
            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
            {title: 'Example 1', inline: 'span', classes: 'example1'},
            {title: 'Example 2', inline: 'span', classes: 'example2'},
            {title: 'Table styles'},
            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
       ],
*/
/*
       formats : {
            alignleft : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'left'},
            aligncenter : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'center'},
            alignright : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'right'},
            alignfull : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'full'},
            bold : {inline : 'span', 'classes' : 'bold'},
            italic : {inline : 'span', 'classes' : 'italic'},
            underline : {inline : 'span', 'classes' : 'underline', exact : true},
            strikethrough : {inline : 'del'},
            forecolor : {inline : 'span', classes : 'forecolor', styles : {color : '%value'}},
            hilitecolor : {inline : 'span', classes : 'hilitecolor', styles : {backgroundColor : '%value'}},
            custom_format : {block : 'h1', attributes : {title : "Header"}, styles : {color : red}}
       },
*/

    });

  }

});
    
</script>
<?php 
} 
?>
          