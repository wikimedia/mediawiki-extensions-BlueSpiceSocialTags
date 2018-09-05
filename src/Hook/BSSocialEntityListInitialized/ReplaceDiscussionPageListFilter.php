<?php

namespace BlueSpice\Social\Tags\Hook\BSSocialEntityListInitialized;

use BlueSpice\Social\Hook\BSSocialEntityListInitialized;
use BlueSpice\Data\FieldType;
use BlueSpice\Data\Filter\Numeric;
use BlueSpice\Data\Filter\ListValue;
use BlueSpice\Social\Topics\Entity\Topic;
use BlueSpice\Social\Renderer\EntityList;
use BlueSpice\Social\Topics\EntityListContext\DiscussionPage;

class ReplaceDiscussionPageListFilter extends BSSocialEntityListInitialized {
	protected function skipProcessing() {
		if( !$this->entityList->getContext() instanceof DiscussionPage ) {
			return true;
		}
		if( !$title = $this->entityList->getContext()->getParent()->getRelatedTitle() ) {
			return true;
		}
		return false;
	}

	protected function doProcess() {
		$this->args[EntityList::PARAM_FILTER] = array_filter(
			$this->args[EntityList::PARAM_FILTER],
			function( $e ) {
				return $e->{Numeric::KEY_PROPERTY} !== Topic::ATTR_DISCUSSION_TITLE_ID;
			}
		);
		$title = $this->entityList->getContext()->getParent()->getRelatedTitle();
		$this->args[EntityList::PARAM_FILTER][] = (object)[
			ListValue::KEY_PROPERTY => 'tags',
			ListValue::KEY_VALUE => [
				$title->getFullText(),
				$title->getOtherPage()->getFullText()
			],
			ListValue::KEY_COMPARISON => ListValue::COMPARISON_CONTAINS,
			ListValue::KEY_TYPE => FieldType::LISTVALUE
		];
		foreach( $this->args[EntityList::PARAM_LOCKED_FILTER_NAMES] as &$name ) {
			if( $name !== Topic::ATTR_DISCUSSION_TITLE_ID ) {
				continue;
			}
			unset( $name );
			return;
		}
		$this->args[EntityList::PARAM_LOCKED_FILTER_NAMES][] = 'tags';
	}

}
