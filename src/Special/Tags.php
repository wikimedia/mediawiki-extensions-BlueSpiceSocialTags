<?php
/**
 * Renders the BlueSpiceSocialGroups special page.
 *
 * Part of BlueSpice MediaWiki
 *
 * @author     Patric Wirth
 * @package    BlueSpiceSocial
 * @subpackage BSSocialBlueSpiceSocialGroups
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 */
namespace BlueSpice\Social\Tags\Special;

use BlueSpice\Context;
use BlueSpice\Data\FieldType;
use BlueSpice\Data\Filter\ListValue;
use BlueSpice\Renderer\Params;
use BlueSpice\Social\Renderer\EntityList;
use BlueSpice\Social\Tags\EntityListContext\SpecialTags;
use BlueSpice\SpecialPage;
use Title;

class Tags extends SpecialPage {

	public function __construct() {
		parent::__construct( 'SocialTags', 'read', false );
	}

	/**
	 *
	 * @param string $param
	 */
	public function execute( $param ) {
		parent::execute( $param );

		$this->getOutput()->setPageTitle(
			$this->msg( 'bs-socialtags-special-heading' )->plain()
		);
		if ( empty( $param ) ) {
			$this->getOutput()->addHTML(
				$this->msg( 'bs-socialtags-special-invalid-title' )->params( $param )->parse()
			);
			return;
		}
		$title = Title::newFromText( $param );
		if ( !$title ) {
			$this->getOutput()->addHTML(
				$this->msg( 'bs-socialtags-special-invalid-title' )->params( $param )->parse()
			);
			return;
		}

		$context = new SpecialTags(
			new Context(
				$this->getContext(),
				$this->getConfig()
			),
			$this->getConfig(),
			$this->getContext()->getUser()
		);
		$filters = $context->getFilters();
		$filters[] = (object)[
			ListValue::KEY_TYPE => FieldType::LISTVALUE,
			ListValue::KEY_COMPARISON => ListValue::COMPARISON_CONTAINS,
			ListValue::KEY_PROPERTY => 'tags',
			ListValue::KEY_VALUE => [ $title->getFullText() ]
		];
		$renderer = MediaWikiServices::getInstance()->getService( 'BSRendererFactory' )->get(
			'entitylist',
			new Params( [
				EntityList::PARAM_CONTEXT => $context,
				EntityList::PARAM_LOCKED_FILTER_NAMES => $context->getLockedFilterNames() + [
					'tags'
				],
				EntityList::PARAM_FILTER => $filters,
				EntityList::PARAM_SHOW_ENTITY_SPAWNER => false,
			] )
		);

		$this->getOutput()->addHTML( $renderer->render() );
	}
}
