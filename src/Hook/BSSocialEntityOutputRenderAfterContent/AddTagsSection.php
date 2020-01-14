<?php

namespace BlueSpice\Social\Tags\Hook\BSSocialEntityOutputRenderAfterContent;

use BlueSpice\Social\Entity;
use BlueSpice\Social\Hook\BSSocialEntityOutputRenderAfterContent;
use Html;

/**
 * Adds a tag count to every non comment entity view
 */
class AddTagsSection extends BSSocialEntityOutputRenderAfterContent {

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$entity = $this->oEntityOutput->getEntity();

		if ( !$entity instanceof Entity ) {
			return true;
		}
		if ( !$entity->exists() || $entity->hasParent() ) {
			return true;
		}
		if ( !$entity->getConfig()->get( 'IsTagable' ) ) {
			return true;
		}

		$status = $entity->userCan( 'tag' );
		if ( !$status->isOK() ) {
			return true;
		}

		$countTags = count( $entity->get( 'tags', [] ) );

		$view = '';
		$view .= Html::openElement( "a", [
			'class' => 'bs-social-entityaftercontent-tags'
		] );

		// Only present on mobile view.
		$view .= Html::element(
			'span',
			[ 'class' => 'bs-social-count-short' ],
			$countTags
		);

		$msg = wfMessage(
			'bs-socialtags-tagstext',
			$countTags
		);

		$view .= Html::element(
			'span',
			[ 'class' => 'bs-social-count-default' ],
			$msg->parse()
		);

		$view .= Html::closeElement( "a" );

		$this->aViews[] = $view;
		return true;
	}
}
