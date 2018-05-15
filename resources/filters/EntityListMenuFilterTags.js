/**
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BluespiceSocial
 * @subpackage BSSocial
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 */

bs.social.EntityListMenuFilterTags = function( key, mVal, EntityListMenu ) {
	bs.social.EntityListMenuFilter.call( this, key, mVal, EntityListMenu );
	var me = this;
	me.selectedFilters = EntityListMenu.filter.tags || [];
	me.allowedFilters = EntityListMenu.filter.tags || [];
	me.field = me.makeField();
	me.initDone = false;
};

OO.initClass( bs.social.EntityListMenuFilterTags );
OO.inheritClass( bs.social.EntityListMenuFilterTags, bs.social.EntityListMenuFilter );

bs.social.EntityListMenuFilterTags.prototype.change = function( mVal ) {
	if( !mVal ) {
		mVal = [];
	}
	bs.social.EntityListMenuFilterTags.super.prototype.change.apply( this, [
		mVal
	]);
};

bs.social.EntityListMenuFilterTags.prototype.makeField = function() {
	var field = $(
		'<label>'
		+ this.getVarLabel( 'tags' )
		+ '<select style="width:100%"></select>'
		+ '</label>'
	);
	this.$element = field;
	return field;
};
bs.social.EntityListMenuFilterTags.prototype.init = function() {
	if( this.initDone ) {
		return;
	}
	var items = [],
		tags = this.selectedFilters,
		me = this;

	var bDisabled = tags.length > 0;
	var $select2 = this.$element.find('select').select2({
		multiple: true,
		placeholder: this.getVarLabel( 'tags' ),
		allowClear: !bDisabled,
		disabled: bDisabled,
		minimumInputLength: 1,
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
		}
	});

	for( var i = 0; i < tags.length; i++ ) {
		$select2.append(
			new Option( tags[i], tags[i], true, true )
		);
	}
	$select2.trigger('change');
	if( bDisabled ) {
		this.initDone = true;
		return;
	}

	this.$element.find( 'select' ).on( 'select2:select', function( e ) {
		me.change( me.$element.find('select').select2( "val" ) );
	});
	this.$element.find( 'select' ).on( 'select2:unselect', function( e ) {
		me.change( me.$element.find('select').select2( "val" ) );
	});
	this.initDone = true;
};

bs.social.EntityListMenuFilters = bs.social.EntityListMenuFilters || {};
bs.social.EntityListMenuFilters.tags = bs.social.EntityListMenuFilterTags;