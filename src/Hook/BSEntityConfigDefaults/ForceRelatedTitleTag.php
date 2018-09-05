<?php

namespace BlueSpice\Social\Tags\Hook\BSEntityConfigDefaults;
use BlueSpice\Hook\BSEntityConfigDefaults;

class ForceRelatedTitleTag extends BSEntityConfigDefaults {

	protected function doProcess() {
		$this->defaultSettings['ForceRelatedTitleTag'] = false;
		return true;
	}
}
