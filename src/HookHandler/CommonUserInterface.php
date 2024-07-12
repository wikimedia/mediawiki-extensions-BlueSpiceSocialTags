<?php

namespace BlueSpice\Social\Tags\HookHandler;

use MWStake\MediaWiki\Component\CommonUserInterface\Hook\MWStakeCommonUILessVarsInit;

class CommonUserInterface implements MWStakeCommonUILessVarsInit {

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonUILessVarsInit( $lessVars ): void {
		$lessVars->setVar( 'bs-social-background-color-foreign', '#D6DCE7' );
	}
}
