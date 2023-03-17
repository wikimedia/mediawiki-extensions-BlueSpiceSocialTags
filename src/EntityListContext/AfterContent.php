<?php

namespace BlueSpice\Social\Tags\EntityListContext;

use BlueSpice\Social\Entity;
use Config;
use IContextSource;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\DataStore\Filter\ListValue;
use Title;
use User;

class AfterContent extends \BlueSpice\Social\EntityListContext {
	public const CONFIG_NAME_OUTPUT_TYPE = 'EntityListAfterContentOutputType';
	public const CONFIG_NAME_TYPE_ALLOWED = 'EntityListAfterContentTypeAllowed';
	public const CONFIG_NAME_TYPE_SELECTED = 'EntityListAfterContentTypeSelected';

	/**
	 *
	 * @var Title
	 */
	protected $title = null;

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param User|null $user
	 * @param Entity|null $entity
	 * @param Title|null $title
	 */
	public function __construct( IContextSource $context, Config $config,
		User $user = null, Entity $entity = null, Title $title = null ) {
		parent::__construct( $context, $config, $user );
		$this->title = $title;
	}

	/**
	 *
	 * @return Title
	 */
	public function getTitle() {
		return $this->title ? $this->title : $this->context->getTitle();
	}

	/**
	 *
	 * @return int
	 */
	public function getLimit() {
		return 3;
	}

	/**
	 *
	 * @return string
	 */
	public function getSortProperty() {
		return Entity::ATTR_TIMESTAMP_TOUCHED;
	}

	/**
	 *
	 * @return bool
	 */
	public function useEndlessScroll() {
		return false;
	}

	/**
	 *
	 * @return \stdClass
	 */
	protected function getTagsFilter() {
		$services = MediaWikiServices::getInstance();
		$talkPageTarget = $services->getNamespaceInfo()->getTalkPage( $this->getTitle() );
		$fullText = $services->getTitleFormatter()->getFullText( $talkPageTarget );
		return (object)[
			ListValue::KEY_PROPERTY => 'tags',
			ListValue::KEY_VALUE => [
				$this->getTitle()->getFullText(),
				$fullText
			],
			ListValue::KEY_COMPARISON => ListValue::COMPARISON_CONTAINS,
			ListValue::KEY_TYPE => 'list'
		];
	}

	/**
	 *
	 * @return array
	 */
	public function getFilters() {
		return array_merge( parent::getFilters(), [ $this->getTagsFilter() ] );
	}

	/**
	 *
	 * @return array
	 */
	public function getLockedFilterNames() {
		return array_merge( parent::getLockedFilterNames(), [ 'tags' ] );
	}
}
