<?php
/**
 * Renders the BlueSpiceSocialGroups special page.
 *
 * Part of BlueSpice MediaWiki
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceSocial
 * @subpackage BSSocialBlueSpiceSocialGroups
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 */
namespace BlueSpice\Social\Tags\Special;

use BlueSpice\Context;
use BlueSpice\Services;
use BlueSpice\Renderer\Params;
use BlueSpice\Social\Tags\EntityListContext\SpecialTags;

class Tags extends \BlueSpice\SpecialPage {

	function __construct() {
		parent::__construct( 'SocialTags', 'read', true );
	}

	function execute( $sParam ) {
		//parent::execute($sParam);
		$this->checkPermissions();

		$this->getOutput()->setPageTitle(
			\wfMessage( 'bs-socialtags-special-heading' )->plain()
		);

		$context = new SpecialTags(
			new Context(
				$this->getContext(),
				$this->getConfig()
			),
			$this->getConfig(),
			$this->getContext()->getUser()
		);
		$renderer = Services::getInstance()->getBSRendererFactory()->get(
			'entitylist',
			new Params( [ 'context' => $context ] )
		);

		$this->getOutput()->addHTML( $renderer->render() );
	}
}