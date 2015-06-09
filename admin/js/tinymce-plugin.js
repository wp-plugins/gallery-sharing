(function( $ ) {
	'use strict';

    if (typeof (tinymce) != "undefined"){
    	var $modal = null;

    	/**
    	 * Add plugin to tinymce editor
    	 */
    	tinymce.PluginManager.add('gallery_sharing_button', function( editor, url ) {
    		/**
			 * add button to editor
			 */
			editor.addButton('gallery_sharing_button', {
				// text: 'Gallery Sharing',
				title: "Gallery Sharing",
				icon: 'ph-gallery-sharing icon-slideshare',
				onclick: function() {	
					/**
					 * opens jquery dialog
					 */
					if($modal != null && $modal.length > 0){
						$modal.dialog("open");
					}
				}
			});
			/**
			 * add dialog element to body and init functionallity
			 */
			$.get(ajaxurl+"?action=ph_gallery_sharing_modal", function(data){
				$modal = $(data);
				$("body").append($modal);
				init_gallery_sharing_dialog(editor, $modal);
			});	

		});
		
    }

    /**
     * initializes all function for gallery sharing modal
     * @param   editor  		tinymce editor
     * @param   $modal 			jQuery dialog element
     */
    function init_gallery_sharing_dialog(editor, $modal){
    	var ph_gallery_sharing_shortcode = '[ph-gallery-sharing id="{id}" source="{source}"]';
    	
		/**
		 * init jquery dialog
		 */
		var width = window.innerWidth;
		if(width > 1000) width = 1000;
		$modal.dialog({
			autoOpen: false,
			draggable: false,
			modal: true,
			title: "Gallery Sharing",
			width: (width-40)
		});

		/**
		 * resize modal on window resize
		 */
		$(window).resize(function(){
			var width = window.innerWidth;
			if(width > 1000) width = 1000;
			$modal.dialog("option", "width", (width-40) );
		});

		/**
		 * value elements of modal
		 */
		var $source = $modal.find("#ph-gallery-sharing-modal-source");
		var $search = $modal.find("#ph-gallery-sharing-modal-search");
		var $list = $modal.find("#ph-gallery-sharing-list");

		/**
		 * autocomplete for search
		 */
		$search.autocomplete({
			source: function(request, response){
				/**
				 * use json oder jsonp
				 */
				var url = "/index.php";
				var dataType = "json";
				var source = $source.val();
				if(source != ""){
					url = "http://"+source+url;
					dataType = "jsonp";
				}

				/**
				 * send autocomplete ajax
				 */
				$.ajax({
		        	url: url,
		        	dataType: dataType,
		        	jsonp: "callback",
		        	type: "GET",
		        	data: {
		            	__api: 1,
		            	__ph_content_sharing: 1,
		            	__action: "search",
		            	__search: request.term,
		        	},
		         	success: function( data ) {
		         		console.log(data);
		         		/**
		         		 * empty result for native jquery autocompelte
		         		 * because we build it on our own
		         		 */
		        		response([]);
		        		/**
		        		 * build list of contents
		        		 */
		        		render_gallery_list(data.result, source );
		        		
		          	},
		          	error: function(jqXHR, textStatus, errorThrown ){
		          		console.log([jqXHR, textStatus, errorThrown ]);
		          	},
		        });
			},

		});

		/**
		 * renders the result of autocomplete to suggestion list
		 */
		function render_gallery_list(galleries, source){
			$list.empty();
			$list.data("source", source);
			console.log(["galleries", galleries]);
			for (var i = 0; i < galleries.length; i++) {
    			$list.append(get_gallery_list_item(galleries[i]));
    		};
		}

		/**
		 * renders a single gallery sharing suggestion list item
		 */
		function get_gallery_list_item(item){
			/**
			 * Title of gallery with ID
			 */
			var $title = $("<span>"+item.post_title+"<small>( ID "+item.ID+" )</small></span>")
						.addClass("ph-gallery-sharing-item-title");

			/**
			 * preview images
			 */
			var $preview = $("<div></div>")
						.addClass("ph-gallery-sharing-preview");

			for (var i = 0; i < item.attachments.length; i++) {
				var attachment = item.attachments[i];
				if(attachment)
					$("<img />").attr("src", attachment[0]).appendTo($preview);
			};

			/**
			 * a litte preview of images
			 */
			var $button = $("<button>Einfügen</button>")
						.addClass("ph-gallery-sharing-item-add button button-primary")
						.data("ID", item.ID);

			var $item = $("<li></li>")
						.addClass("ph-gallery-sharing-item");

			$item.append($title);
			$item.append($preview);
			$item.append($button);
			return $item;
		}

		/**
		 * gallery suggestion item click listener
		 * adds shortcode to editor
		 */
		$list.on("click", ".ph-gallery-sharing-item-add", function(e){
			/**
			 * build the shortcode with shortcode pattern
			 */
			var shortcode = ph_gallery_sharing_shortcode
							.replace("{id}", $(this).data("ID") )
							.replace("{source}", $list.data("source") );
			/**
			 * insert shortcode into editor
			 */
			editor.insertContent(shortcode);
			$modal.dialog("close");
		});
    }
	

})( jQuery );
