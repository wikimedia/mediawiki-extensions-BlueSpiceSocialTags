<?php

namespace BlueSpice\Social\Tags\Hook\BSSocialEntityOutputRenderBeforeContent;
use BlueSpice\Social\Hook\BSSocialEntityOutputRenderBeforeContent;

class AddTagInfo extends BSSocialEntityOutputRenderBeforeContent {

	protected function doProcess() {
		$aData = $this->oEntityOutput->getEntity()->getFullData();

		if( empty( $aData['tags'] ) ) {
			return true;
		}

		$this->sOut .= \Html::openElement( 'span', [
			'class' => 'bs-social-beforecontent-tags',
		]);

		foreach( $aData['tags'] as $sTag ) {
			if( !$oTitle = \Title::newFromText( $sTag ) ) {
				continue;
			}

			$this->sOut .= \Html::element(
				'a',
				[ 'href' => $oTitle->getLocalURL() ],
				"#$sTag"
			);
		}
		$this->sOut .= \Html::closeElement( 'span' );
		return true;
	}
}