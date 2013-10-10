<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\TestFramework\Application $this */

/**
 * @var \Magento\Core\Model\Config\Value $configData
 */
$configData = $this->getObjectManager()->create('Magento\Core\Model\Config\Value');
$configData->setPath('carriers/flatrate/active')
    ->setScope(\Magento\Core\Model\Config::SCOPE_DEFAULT)
    ->setScopeId(0)
    ->setValue(1)
    ->save();

$this->getObjectManager()->get('Magento\Core\Model\CacheInterface')
    ->clean(array(\Magento\Core\Model\Config::CACHE_TAG));
