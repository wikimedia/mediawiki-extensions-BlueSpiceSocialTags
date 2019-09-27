<?php
namespace BlueSpice\Social\Tags\Hook\TitleMoveComplete;

use MWException;
use JobQueueGroup;
use BlueSpice\Context;
use BlueSpice\Services;
use BlueSpice\Social\Entity;
use BlueSpice\Data\ReaderParams;
use BlueSpice\Data\Filter\ListValue;
use BlueSpice\Social\Tags\Job\UpdateTags as Job;
use BlueSpice\Social\Data\Entity\Store;
use BlueSpice\Social\Tags\EntityListContext\SpecialTags;

class UpdateTags extends \BlueSpice\Hook\TitleMoveComplete {

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$context = new Context(
			$this->getContext(),
			$this->getConfig()
		);
		$serviceUser = Services::getInstance()->getBSUtilityFactory()
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
			ListValue::KEY_VALUE => [ $this->title->getFullText() ],
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
			$entity = Services::getInstance()->getBSEntityFactory()
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
			[ $this->title->getFullText() ]
		);
		$tags[] = $this->newTitle->getFullText();
		$tags = array_values( array_unique( $tags ) );

		$job = new Job(
			$entity->getTitle(),
			[ 'tags' => $tags ]
		);
		JobQueueGroup::singleton()->push(
			$job
		);
	}
}
