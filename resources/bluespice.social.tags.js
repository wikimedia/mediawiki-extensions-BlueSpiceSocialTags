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

$( document ).on( 'BSSocialEntityInit', function( event, Entity ) {
	if( !Entity.getConfig().IsTagable ) {
		return;
	}
	if( Entity.hasParent() ) {
		return;
	}

	var $lnk = Entity.getContainer( Entity.AFTER_CONTENT_CONTAINER )
			.find( '.bs-social-entityaftercontent-tags' );

	$lnk.on('click', function() {
		if( !Entity.$tagEditor ) {
			Entity.$tagEditor = $(
				'<div class="bs-social-entity-aftercontent-tageditor"></div>'
			).hide();
			Entity.$tagEditor.insertAfter(
				Entity.getEl().find('.bs-social-entity-aftercontent').first()
			);
			Entity.$tagSelection = $(
				'<div><select style="width:100%"></select></div>'
			);
			Entity.tagSubmitButton = new OO.ui.ButtonInputWidget( {
				label: mw.message( 'bs-social-editor-ok' ).plain(),
				flags: ['progressive', 'primary'],
				align: 'right'
			});
			Entity.tagCancelButton = new OO.ui.ButtonInputWidget( {
				label: mw.message( 'bs-social-editor-cancel' ).plain(),
				align: 'right'
			});
			Entity.tagSubmitButton.on( 'click', function() {
				var val = Entity.$tagSelection.find('select').select2( "val" );
				if( !val ) {
					val = [];
				}
				Entity.showLoadMask();
				bs.api.tasks.execSilent(
					'socialtags',
					'editTags',
					{ id: Entity.id, type: Entity.type, tags: val }
				).done( function( response ) {
					Entity.replaceEL( response.payload.view );
				})
				.then(function(){
					Entity.hideLoadMask();
				});
			});
			Entity.tagCancelButton.on( 'click', function() {
				Entity.$tagEditor.toggle();
				Entity.$tagEditor.remove();
				Entity.$tagEditor = null;
			});

			var items = [], tags = Entity.data.get('tags', []);
			for( var i = 0; i < tags.length; i++ ) {
				items.push( { id: tags[i], text: tags[i] });
			}

			Entity.$tagEditor.append( Entity.$tagSelection );
			Entity.$tagEditor.append( Entity.tagCancelButton.$element );
			Entity.$tagEditor.append( Entity.tagSubmitButton.$element );
			var $select2 = Entity.$tagSelection.find('select').select2({
				multiple: true,
				allowClear: true,
				ajax: {
					url: mw.util.wikiScript( 'api' ),
					dataType: 'json',
					tape: 'POST',
					data: function (params) {
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
			for( var i = 0; i < tags.length; i++ ) {
				$select2.append(
					new Option( tags[i], tags[i], true, true )
				);
			}
			$select2.trigger('change');
		}

		Entity.$tagEditor.toggle();
	});
});
