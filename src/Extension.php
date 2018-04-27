<?php
/**
 * BlueSpiceSocial base extension for BlueSpice
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceSocial
 * @subpackage BlueSpiceSocialTags
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 */

namespace BlueSpice\Social\Tags;

use BlueSpice\Social\Entities;
use BlueSpice\Social\Topics\Entity\Discussion;

class Extension extends \BlueSpice\Extension {

	/**
	 * HACKY!
	 * @param string $sOut
	 * @param array $aElOptions
	 * @param Status $oStatus
	 * @param mixed $mContext
	 * @param array $aCallbacks
	 */
	public static function onBSSocialEntitiesmakeList( &$sOut, &$aElOptions, $oStatus, $mContext, &$aCallbacks, &$sSuffix, &$sPrefix ) {
		if( !$mContext || !$mContext instanceof Discussion ) {
			return true;
		}
		$aButtonClasses = [
			'bs-socialtags-relatedentities-contentswitch',
			'mw-ui-button',
			'mw-ui-progressive'
		];
		$sPrefix .= \Html::input(
			'contentswitch',
			wfMessage( 'bs-socialtags-contentswitch-relatedbytag' )->plain(),
			'button',
			[ 'class' => implode( ' ', $aButtonClasses ) ]
		);
		$sPrefix .= \Html::openElement( 'div', [
			'class' => 'bs-socialtags-relatedentities-left',
		]);
		//list goes here
		$sSuffix .= \Html::closeElement( 'div' );
		$sSuffix .= \Html::openElement( 'div', [
			'class' => 'bs-socialtags-relatedentities-right',
			'style' => 'display:none;',
		]);
		$sSuffix .= \Html::openElement( 'div', [
			'class' => 'bs-socialtags-relatedentities',
		]);
		$sSuffix .= Entities::makeList(
			[],
			[
				'tags' => [
					$mContext->getRelatedTitle()->getFullText(),
					\Title::newFromText(
						$mContext->getRelatedTitle()->getText(),
						$mContext->getRelatedTitle()->getNamespace()-1
					)->getFullText()
				],
			],
			0,
			[],
			(object)[
				'type' => 'BlueSpiceSocialTags',
				'title' => $mContext->getRelatedTitle(),
			]
		);
		$sSuffix .= \Html::closeElement( 'div' );
		$sSuffix .= \Html::closeElement( 'div' );
		return true;
	}
}