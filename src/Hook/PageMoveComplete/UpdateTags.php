<?php
namespace BlueSpice\Social\Tags\Hook\PageMoveComplete;

use BlueSpice\Context;
use BlueSpice\Hook\PageMoveComplete;
use BlueSpice\Social\Data\Entity\Store;
use BlueSpice\Social\Entity;
use BlueSpice\Social\Tags\EntityListContext\SpecialTags;
use BlueSpice\Social\Tags\Job\UpdateTags as Job;
use MediaWiki\MediaWikiServices;
use MWException;
use MWStake\MediaWiki\Component\DataStore\Filter\ListValue;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;
use Title;

class UpdateTags extends PageMoveComplete {

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$context = new Context(
			$this->getContext(),
			$this->getConfig()
		);
		$serviceUser = MediaWikiServices::getInstance()->getService( 'BSUtilityFactory' )
			->getMaintenanceUser()->getUser();

		$listContext = new SpecialTags(
			$context,
			$context->getConfig(),
			$serviceUser,
			null
		);
		$filters = $listContext->getFilters();
		$filters[] = (object)[
			ListValue::KEY_PROPERTY => 'tags',
			ListValue::KEY_VALUE => [ Title::newFromLinkTarget( $this->old )->getFullText() ],
			ListValue::KEY_COMPARISON => ListValue::COMPARISON_CONTAINS,
			ListValue::KEY_TYPE => 'list'
		];

		$params = new ReaderParams( [
			'filter' => $filters,
			'sort' => $listContext->getSort(),
			'limit' => ReaderParams::LIMIT_INFINITE,
			'start' => 0,
		] );
		$res = $this->getStore()->getReader( $listContext )->read( $params );
		foreach ( $res->getRecords() as $record ) {
			$entity = MediaWikiServices::getInstance()->getService( 'BSEntityFactory' )
				->newFromObject( $record->getData() );
			if ( !$entity instanceof Entity || !$entity->exists() ) {
				continue;
			}
			$this->addJob( $entity );
		}

		return true;
	}

	/**
	 *
	 * @return Store
	 * @throws MWException
	 */
	protected function getStore() {
		return new Store();
	}

	/**
	 *
	 * @param Entity $entity
	 */
	protected function addJob( Entity $entity ) {
		$tags = array_diff(
			$entity->get( 'tags', [] ),
			[ Title::newFromLinkTarget( $this->old )->getFullText() ]
		);
		$tags[] = Title::newFromLinkTarget( $this->new )->getFullText();
		$tags = array_values( array_unique( $tags ) );

		$job = new Job(
			$entity->getTitle(),
			[ 'tags' => $tags ]
		);
		MediaWikiServices::getInstance()->getJobQueueGroup()->push(
			$job
		);
	}
}
