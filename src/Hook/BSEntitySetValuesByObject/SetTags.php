<?php

namespace BlueSpice\Social\Tags\Hook\BSEntitySetValuesByObject;
use BlueSpice\Hook\BSEntitySetValuesByObject;
use BlueSpice\Social\Entity;
use BlueSpice\Social\Entity\Action;

class SetTags extends BSEntitySetValuesByObject {

	protected function checkEntity() {
		if( !$this->entity->getConfig( 'IsTagable' ) ) {
			return false;
		}
		if( $this->entity->hasParent() ) {
			return false;
		}
		if( !$this->entity->exists() ) {
			return false;
		}
		return true;
	}

	protected function tagActionEntity() {
		if( !$this->entity->getRelatedTitle() ) {
			return false;
		}
		//autotag action entities!
		$this->entity->tags = [
			$this->entity->getRelatedTitle()->getFullText()
		];
		return true;
	}

	protected function doProcess() {
		if( !$this->entity instanceof Entity ) {
			return true;
		}
		if( empty( $this->entity->tags ) ) {
			$this->entity->tags = [];
		}
		if( !$this->checkEntity() ) {
			return true;
		}

		if( !empty( $this->data->tags ) ) {
			$this->entity->tags = $this->data->tags;
		} elseif( $this->entity instanceof Action ) {
			$this->tagActionEntity();
		}

		return true;
	}
}

