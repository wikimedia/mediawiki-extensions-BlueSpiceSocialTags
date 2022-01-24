<?php

namespace BlueSpice\Social\Tags\Hook\BSSocialEntityOutputRenderBeforeContent;

use BlueSpice\Social\Hook\BSSocialEntityOutputRenderBeforeContent;
use Html;
use Title;

class AddTagInfo extends BSSocialEntityOutputRenderBeforeContent {

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$data = $this->oEntityOutput->getEntity()->getFullData();

		if ( empty( $data['tags'] ) ) {
			return true;
		}

		$this->sOut .= Html::openElement( 'span', [
			'class' => 'bs-social-beforecontent-tags',
		] );

		// show only first tag which is not a talkpage in entity
		// all others are available in dropdown menu in entity
		foreach ( $data['tags'] as $tag ) {
			$title = Title::newFromText( $tag );
			if ( !$title ) {
				continue;
			}
			if ( $title->isTalkPage() ) {
				continue;
			}
			$this->sOut .= Html::element(
				'a',
				[ 'href' => $title->getLocalURL() ],
				"#$tag"
			);
			break;
		}
		$this->sOut .= Html::closeElement( 'span' );
		return true;
	}
}
