<?php

namespace BlueSpice\Social\Tags\Hook\BSSocialEntityListInitialized;

use BlueSpice\Social\Hook\BSSocialEntityListInitialized;
use BlueSpice\Social\Renderer\EntityList;
use BlueSpice\Social\Topics\Entity\Topic;
use BlueSpice\Social\Topics\EntityListContext\DiscussionPage;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\DataStore\FieldType;
use MWStake\MediaWiki\Component\DataStore\Filter\ListValue;
use MWStake\MediaWiki\Component\DataStore\Filter\Numeric;

class ReplaceDiscussionPageListFilter extends BSSocialEntityListInitialized {
	/**
	 *
	 * @return bool
	 */
	protected function skipProcessing() {
		if ( !$this->entityList->getContext() instanceof DiscussionPage ) {
			return true;
		}
		$title = $this->entityList->getContext()->getParent()->getRelatedTitle();
		if ( !$title ) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->args[EntityList::PARAM_FILTER] = array_filter(
			$this->args[EntityList::PARAM_FILTER],
			function ( $e ) {
				return $e->{Numeric::KEY_PROPERTY} !== Topic::ATTR_DISCUSSION_TITLE_ID;
			}
		);
		$title = $this->entityList->getContext()->getParent()->getRelatedTitle();
		$value = [];
		$value[0] = $title->getFullText();
		$services = MediaWikiServices::getInstance();
		$namespaceInfo = $services->getNamespaceInfo();
		if ( $namespaceInfo->getAssociatedPage( $title ) ) {
			$associatedPage = $namespaceInfo->getAssociatedPage( $title );
			$value[1] = $services->getTitleFormatter()->getFullText( $associatedPage );
		}

		$this->args[EntityList::PARAM_FILTER][] = (object)[
			ListValue::KEY_PROPERTY => 'tags',
			ListValue::KEY_VALUE => $value,
			ListValue::KEY_COMPARISON => ListValue::COMPARISON_CONTAINS,
			ListValue::KEY_TYPE => FieldType::LISTVALUE
		];
		foreach ( $this->args[EntityList::PARAM_LOCKED_FILTER_NAMES] as &$name ) {
			if ( $name !== Topic::ATTR_DISCUSSION_TITLE_ID ) {
				continue;
			}
			unset( $name );
			break;
		}
		$this->args[EntityList::PARAM_LOCKED_FILTER_NAMES][] = 'tags';

		return true;
	}

}
