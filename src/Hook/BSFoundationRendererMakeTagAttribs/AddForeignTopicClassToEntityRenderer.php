<?php

namespace BlueSpice\Social\Tags\Hook\BSFoundationRendererMakeTagAttribs;

use BlueSpice\Hook\BSFoundationRendererMakeTagAttribs;
use BlueSpice\Social\Entity as SocialEntity;
use BlueSpice\Social\Renderer\Entity;
use BlueSpice\Social\Topics\EntityListContext\DiscussionPage;
use MediaWiki\MediaWikiServices;
use Title;

class AddForeignTopicClassToEntityRenderer extends BSFoundationRendererMakeTagAttribs {

	protected function skipProcessing() {
		if ( !$this->renderer instanceof Entity ) {
			return true;
		}
		if ( $this->renderer->getEntity()->get( SocialEntity::ATTR_TYPE ) !== 'topic' ) {
			return true;
		}
		if ( !$this->renderer->getContext() instanceof DiscussionPage ) {
			// currently foreign topics will not be displayed in after article
			// content
			return true;
		}

		if ( !$this->renderer->getEntity()->getRelatedTitle() ) {
			return true;
		}
		if ( !$this->renderer->getContext()->getTitle() ) {
			return true;
		}
		if ( $this->renderer->getEntity()->getRelatedTitle()->getNamespace() < 0 ) {
			return true;
		}
		if ( $this->renderer->getContext()->getTitle()->getNamespace() < 0 ) {
			return true;
		}
		$namespaceInfo = MediaWikiServices::getInstance()->getNamespaceInfo();
		$titleTarget = $namespaceInfo->getTalkPage(
			$this->renderer->getEntity()->getRelatedTitle()
		);
		$title = Title::newFromLinkTarget( $titleTarget );
		$ctxTitle = $namespaceInfo->getTalkPage(
			$this->renderer->getContext()->getTitle()
		);

		if ( !$title || !$ctxTitle ) {
			return true;
		}
		if ( $title->equals( $ctxTitle ) ) {
			return true;
		}

		return false;
	}

	protected function doProcess() {
		if ( empty( $this->attribs['class'] ) ) {
			$this->attribs['class'] = '';
		}
		$this->attribs['class'] .= ' foreign';
	}

}
