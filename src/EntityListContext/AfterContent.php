<?php

namespace BlueSpice\Social\Tags\EntityListContext;

use BlueSpice\Data\Filter\ListValue;
use BlueSpice\Social\Entity;

class AfterContent extends \BlueSpice\Social\EntityListContext {
	const CONFIG_NAME_OUTPUT_TYPE = 'EntityListAfterContentOutputType';
	const CONFIG_NAME_TYPE_ALLOWED = 'EntityListAfterContentTypeAllowed';
	const CONFIG_NAME_TYPE_SELECTED = 'EntityListAfterContentTypeSelected';

	/**
	 *
	 * @var \Title
	 */
	protected $title = null;

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 */
	public function __construct( \IContextSource $context, \Config $config, \User $user = null, Entity $entity = null, \Title $title = null ) {
		parent::__construct( $context, $config, $user );
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title ? $this->title : $this->context->getTitle();
	}

	public function getLimit() {
		return 3;
	}

	public function getSortProperty() {
		return Entity::ATTR_TIMESTAMP_TOUCHED;
	}

	public function useEndlessScroll() {
		return false;
	}

	protected function getTagsFilter() {
		return (object)[
			ListValue::KEY_PROPERTY => 'tags',
			ListValue::KEY_VALUE => [
				$this->getTitle()->getFullText(),
				$this->getTitle()->getTalkPage()->getFullText()
			],
			ListValue::KEY_COMPARISON => ListValue::COMPARISON_CONTAINS,
			ListValue::KEY_TYPE => 'list'
		];
	}

	public function getFilters() {
		return array_merge( parent::getFilters() , [ $this->getTagsFilter() ] );
	}

	public function getLockedFilterNames() {
		return array_merge( parent::getLockedFilterNames(), [ 'tags' ] );
	}
}
