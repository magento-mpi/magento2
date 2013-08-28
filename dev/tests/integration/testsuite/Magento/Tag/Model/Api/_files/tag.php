<?php
/**
 * Fixture for tag.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
/** @var Magento_Tag_Model_Tag $tag */
$tag = Mage::getModel('Magento_Tag_Model_Tag');
$tag->setName('tag_name')->setStatus(Magento_Tag_Model_Tag::STATUS_APPROVED);
$tag->save();
