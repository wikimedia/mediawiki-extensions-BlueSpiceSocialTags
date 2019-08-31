<?php

namespace BlueSpice\Social\Tags\Hook\BSSocialEntityOutputRenderAfterContent;
use BlueSpice\Social\Hook\BSSocialEntityOutputRenderAfterContent;
use BlueSpice\Social\Entity;

/**
 * Adds a tag count to every non comment entity view
 */
class AddTagsSection extends BSSocialEntityOutputRenderAfterContent {

	protected function doProcess() {
		$oEntity = $this->oEntityOutput->getEntity();

		if( !$oEntity instanceof Entity ) {
			return true;
		}
		if( !$oEntity->exists() || $oEntity->hasParent() ) {
			return true;
		}
		if( !$oEntity->getConfig()->get( 'IsTagable' ) ) {
			return true;
		}

		$oStatus = $oEntity->userCan( 'tag' );
		if( !$oStatus->isOK() ) {
			return true;
		}

		$countTags = count( $oEntity->get( 'tags', [] ) );

		$sView = '';
		$sView .= \XML::openElement("a", array(
			'class' => 'bs-social-entityaftercontent-tags'
		));

		// Only present on mobile view.
		$sView .= \Html::element(
			'span',
			['class' => 'bs-social-count-short'],
			$countTags
		);

		$msg = wfMessage(
			'bs-socialtags-tagstext',
			$countTags
		);

		$sView .= \Html::element(
			'span',
			['class' => 'bs-social-count-default'],
			$msg->parse()
		);

		$sView .= \XML::closeElement( "a" );

		$this->aViews[] = $sView;
		return true;
	}
}