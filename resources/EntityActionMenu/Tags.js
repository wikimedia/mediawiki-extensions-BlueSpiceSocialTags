/*
* @author     Stefan Kühn
* @package    BluespiceSocial
* @subpackage BlueSpiceSocial
* @copyright  Copyright (C) 2020 Hallo Welt! GmbH, All rights reserved.
* @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
*/

bs.social = bs.social || {};
bs.social.EntityActionMenuTags = bs.social.EntityActionMenu || {};
bs.social.EntityActionMenuTags.Tags = function ( entityActionMenu, data ) {
	OO.EventEmitter.call( this );
	var me = this;
	me.data = data || {};
	me.entityActionMenu = entityActionMenu;
	var countTags = me.entityActionMenu.entity.data.values.tags.length;

	me.$element = null;
	me.$element = $( '<li><a class="dropdown-item bs-social-entityaftercontent-tags">'
		+ mw.message( 'bs-socialtags-tagstext', countTags ).text()
		+ '</a></li>'
	);

	me.$element.on( 'click', function( e ) { me.click( e ); } );
	me.priority = 40;
};

OO.initClass( bs.social.EntityActionMenuTags.Tags );
OO.mixinClass( bs.social.EntityActionMenuTags.Tags, OO.EventEmitter );

bs.social.EntityActionMenuTags.Tags.prototype.click = function (e) {
	var me = this;
	var menuEntity = me.entityActionMenu.entity;

	if( !menuEntity.getConfig().isTagable ) {
		return;
	}
	if( menuEntity.hasParent() ) {
		return;
	}

	if( !menuEntity.$tagEditor ) {
		menuEntity.$tagEditor = $(
			'<div class="bs-social-entity-aftercontent-tageditor"></div>'
		).hide();
		menuEntity.$tagEditor.insertAfter(
			menuEntity.getEl().find('.bs-social-entity-aftercontent').first()
		);
		menuEntity.$tagSelection = $(
			'<div><select style="width:100%"></select></div>'
		);
		menuEntity.tagSubmitButton = new OO.ui.ButtonInputWidget( {
			label: mw.message( 'bs-social-editor-ok' ).plain(),
			flags: ['progressive', 'primary'],
			align: 'right'
		});
		menuEntity.tagCancelButton = new OO.ui.ButtonInputWidget( {
			label: mw.message( 'bs-social-editor-cancel' ).plain(),
			align: 'right'
		});
		menuEntity.tagSubmitButton.on( 'click', function() {
			var val = menuEntity.$tagSelection.find( 'select' ).select2( "val" );
			if( !val ) {
				val = [];
			}
			menuEntity.showLoadMask();
			bs.api.tasks.execSilent(
				'socialtags',
				'editTags',
				{ id: menuEntity.id,
					type: menuEntity.type,
					tags: val
				}
			).done( function( response ) {
				menuEntity.replaceEL( response.payload.view );
			})
			.then(function(){
				menuEntity.hideLoadMask();
			});
		});
		menuEntity.tagCancelButton.on( 'click', function() {
			menuEntity.$tagEditor.toggle();
			menuEntity.$tagEditor.remove();
			menuEntity.$tagEditor = null;
		});

		var items = [], tags = menuEntity.data.get( 'tags' , []);
		for( var i = 0; i < tags.length; i++ ) {
			items.push( { id: tags[i], text: tags[i] });
		}

		menuEntity.$tagEditor.append (
			menuEntity.$tagSelection
		);
		menuEntity.$tagEditor.append(
			menuEntity.tagCancelButton.$element
		);
		menuEntity.$tagEditor.append(
			menuEntity.tagSubmitButton.$element
		);

		var $select2 = menuEntity.$tagSelection.find( 'select' ).select2({
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
		$select2.trigger( 'change' );
	}

	menuEntity.$tagEditor.toggle();
	e.preventDefault();
	return false;
};
