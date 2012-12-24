(function($) {
	jQuery(document).ready(function( ) {
		$("div.tabs-panel").css("max-height", "500px");
		//サブカテゴリー全表示する

		if ( jQuery.isEmptyObject( $("#_excludes_taxonomies") ) ){
			var _excludes_taxonomies = $("#_excludes_taxonomies").val();
			var array_excludes = _excludes_taxonomies.split(',');
		}else{
			var array_excludes =  new Array();
		}

		$( "div.tabs-panel" ).each(function(){
			var tabs_panel = $(this);
			var taxonomy = tabs_panel.attr("id").replace('-all' , '');

			if ( tabs_panel.css("display") == "block" && jQuery.inArray(taxonomy, array_excludes) <  0 ){
				tabs_panel.fadeIn( 500 );

				//子カテゴリーがあるLIに展開ボタンを追加する
				tabs_panel.children( "ul.categorychecklist" ).find("li").has("ul").each(function(){
					var the_p_li = $(this);
					
					//小カテゴリーがある親Inputを取得
					var the_p_label = the_p_li.children("label");
					the_p_label.before('<input type="button" class="button button-small" id="b_'+the_p_li.attr("id")+'" name="combine_category" value="+" style="margin-left: -10px;padding: 0 5px;">');
					//初期状態、選べているカテゴリーを展開する
					var the_chk = the_p_li.has("ul").find("input[type=checkbox]:checked");

					if ( the_chk.is(':checked')){
						var the_button = the_p_li.has("ul").find( "input[name=combine_category]");
						the_button.val('-');
						//console.log(the_button);
					}else{
						//選べないカテゴリー親とも閉じる
						var the_c_ul = the_p_li.has("ul").find('ul').hide( );
					}

			    }); 
			}//end tabs_panel.css("display") == "block" 
		});

		//展開、閉じるのイベントです
		$( "input[name=combine_category]" ).click(function( ){
			var the_b = $(this);
			var the_id = the_b.attr("id").replace('b_' , '');
			var the_li = $("#"+the_id);


			var the_li_ul = the_li.children("ul");
			if ( the_li_ul.css("display") == "block" ){
				the_li_ul.fadeOut(300);
				the_b.val('+');
			}else{
				the_li_ul.fadeIn(500);
				the_b.val('-');
			}
		});
	});
})(jQuery);
