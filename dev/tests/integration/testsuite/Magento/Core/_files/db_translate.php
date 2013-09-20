<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Core\Model\Resource\Translate\String $translateString */
$translateString = \Mage::getModel('Magento\Core\Model\Resource\Translate\String');
$translateString->saveTranslate('Fixture String', 'Fixture Db Translation');
