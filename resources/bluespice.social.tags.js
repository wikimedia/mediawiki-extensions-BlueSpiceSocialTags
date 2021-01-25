$( document ).on( 'BSSocialEntityEditorAdvancedFieldset', function( event, EntityEditor, advancedfieldset ) {
	if( !EntityEditor.getEntity().getConfig().IsTagable ) {
		return;
	}
	if( EntityEditor.getEntity().hasParent() ) {
		//nmb anymore
		return;
	}

	var tags = {};
	tags.$element = $(
		'<div><select style="width:100%"></select></div>'
	);
	tags.setElementGroup = function() {};
	tags.select2 = tags.$element.find( 'select' ).select2({
		multiple: true,
		allowClear: true,
		escapeMarkup: function( element ) {
			return element;
		},
		ajax: {
			url: mw.util.wikiScript( 'api' ),
			dataType: 'json',
			tape: 'POST',
			data: function( params ) {
				return {
					action: 'bs-socialtitlequery-store',
					query: params.term
				};
			},
			processResults: function (data) {
				var results = [];
				$.each(data.results, function (index, result) {
					if( result.type && result.type === 'namespace' ) {
						return;
					}
					results.push({
						id: result.displayText,
						text: result.displayText
					});
				});
				return {
					results: results
				};
			}
		},
		minimumInputLength: 1
	});

	for( var i = 0; i < EntityEditor.getEntity().data.get( 'tags', [] ).length; i++ ) {
		var tag = EntityEditor.getEntity().data.get( 'tags' )[i];
		tags.select2.append(
			new Option( tag, tag, true, true )
		);
	}
	tags.select2.trigger( 'change' );

	EntityEditor.tags = tags;
	EntityEditor.fields.tags = EntityEditor.tags;
	advancedfieldset.addItems( [
		EntityEditor.tags
	]);
});

$( document ).bind( 'BSSocialEntityActionMenuInit', function( event, EntityActionMenu ) {
	EntityActionMenu.classes.tags = bs.social.EntityActionMenuTags.Tags;
});

$( document ).on( "click", '.bs-social-entity-aftercontent-tageditor li.select2-selection__choice', function( e ) {
	window.open( mw.util.getUrl( $( e.target ).attr( 'title' ) ), '_blank' );
} );