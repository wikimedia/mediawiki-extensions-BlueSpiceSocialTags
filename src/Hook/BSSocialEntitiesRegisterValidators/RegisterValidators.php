<?php

namespace BlueSpice\Social\Tags\Hook\BSSocialEntitiesRegisterValidators;
use BlueSpice\Social\Hook\BSSocialEntitiesRegisterValidators;

class RegisterValidators extends BSSocialEntitiesRegisterValidators {

	protected static $aValidators = [
		"\\BlueSpice\\Social\\Tags\Validator\\Filter\\Tags",
	];

	protected function doProcess() {
		$this->aFilterValidators = array_merge(
			$this->aFilterValidators,
			static::$aValidators
		);
		return true;
	}
}