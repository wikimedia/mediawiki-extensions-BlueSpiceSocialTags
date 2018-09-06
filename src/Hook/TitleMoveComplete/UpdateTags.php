<?php
namespace BlueSpice\Social\Tags\Hook\TitleMoveComplete;

use BlueSpice\Context;
use BlueSpice\Services;
use BlueSpice\Social\Entity;
use BlueSpice\Data\ReaderParams;
use BlueSpice\Data\Filter\ListValue;
use BlueSpice\Social\Tags\EntityListContext\SpecialTags;

class UpdateTags extends \BlueSpice\Hook\TitleMoveComplete {
	
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

		$params = new ReaderParams([
			'filter' => $filters,
			'sort' => $listContext->getSort(),
			'limit' => ReaderParams::LIMIT_INFINITE,
			'start' => 0,
		]);
		$res = $this->getStore( $listContext )->getReader()->read( $params );
		foreach( $res->getRecords() as $record ) {
			$entity = Services::getInstance()->getBSEntityFactory()
				->newFromObject( $record->getData() );
			if( !$entity instanceof Entity || !$entity->exists() ) {
				continue;
			}
			$this->addJob( $entity );
		}
	}

	/**
	 *
	 * @param SpecialTags $context
	 * @return \BlueSpice\Social\Data\Entity\Store
	 * @throws \MWException
	 */
	protected function getStore( SpecialTags $context ) {
		return new \BlueSpice\Social\Data\Entity\Store( $context );
	}

	protected function addJob( Entity $entity ) {
		$tags = array_diff(
			$entity->get( 'tags', [] ),
			[$this->title->getFullText()]
		);
		$tags[] = $this->newTitle->getFullText();
		$tags = array_values( array_unique( $tags ) );

		$job = new \BlueSpice\Social\Tags\Job\UpdateTags(
			$entity->getTitle(),
			[ 'tags' => $tags ]
		);
		\JobQueueGroup::singleton()->push(
			$job
		);
	}
}
