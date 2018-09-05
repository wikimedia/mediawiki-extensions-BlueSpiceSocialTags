<?php

namespace BlueSpice\Social\Tags\Hook;
use BlueSpice\Social\Entity;

abstract class BSSocialTagsForceTags extends \BlueSpice\Hook {

	/**
	 *
	 * @var Entity
	 */
	protected $entity = null;

	/**
	 *
	 * @var array
	 */
	protected $tags = null;

	/**
	 *
	 * @param Entity $entity
	 * @param array $tags
	 * @return boolean
	 */
	public static function callback( $entity, &$tags ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$entity,
			$tags
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param Entity $entity
	 * @param array $tags
	 */
	public function __construct( $context, $config, $entity, &$tags ) {
		parent::__construct( $context, $config );

		$this->entity = $entity;
		$this->tags = &$tags;
	}
}