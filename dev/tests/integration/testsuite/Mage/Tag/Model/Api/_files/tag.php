<?php
/**
 * Fixture for tag.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
/** @var Mage_Tag_Model_Tag $tag */
$tag = Mage::getModel('Mage_Tag_Model_Tag');
$tag->setName('tag_name')->setStatus(Mage_Tag_Model_Tag::STATUS_APPROVED);
$tag->save();
