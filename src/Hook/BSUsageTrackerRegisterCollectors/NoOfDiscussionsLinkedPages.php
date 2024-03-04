<?php

namespace BlueSpice\Social\Tags\Hook\BSUsageTrackerRegisterCollectors;

use BlueSpice\Social\Data\Entity\Store;
use BlueSpice\Social\Topics\Entity\Topic;
use BS\UsageTracker\Hook\BSUsageTrackerRegisterCollectors;
use MWStake\MediaWiki\Component\DataStore\FieldType;
use MWStake\MediaWiki\Component\DataStore\Filter\StringValue;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;

class NoOfDiscussionsLinkedPages extends BSUsageTrackerRegisterCollectors {

	protected function doProcess() {
		$store = new Store;
		$res = $store->getReader( $this->getContext() )
			->read(	new ReaderParams( $this->getParams() ) );

		$noOfDiscussionsLinkedPages = 0;
		foreach ( $res->getRecords() as $record ) {
			if ( count( $record->get( 'tags' ) ) > 2 ) {
				$noOfDiscussionsLinkedPages++;
			}
		}

		$this->collectorConfig['no-of-discussions-linked-pages'] = [
			'class' => 'Basic',
			'config' => [
				'identifier' => 'no-of-discussions-linked-pages',
				'internalDesc' => 'Number of Discussions with more than 2 Linked Pages',
				'count' => $noOfDiscussionsLinkedPages
			]
		];
	}

	/**
	 * @return array
	 */
	protected function getParams(): array {
		return [
			ReaderParams::PARAM_LIMIT => ReaderParams::LIMIT_INFINITE,
			ReaderParams::PARAM_FILTER => $this->getFilter()
		];
	}

	/**
	 * @return array
	 */
	protected function getFilter(): array {
		return [ [
			StringValue::KEY_PROPERTY => Topic::ATTR_TYPE,
			StringValue::KEY_TYPE => FieldType::STRING,
			StringValue::KEY_COMPARISON => StringValue::COMPARISON_EQUALS,
			StringValue::KEY_VALUE => Topic::TYPE,
		] ];
	}
}
