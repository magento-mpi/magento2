<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\TestFramework\Application $this */

/**
 * @var \Magento\App\Config\ValueInterface $configData
 */
$configData = $this->getObjectManager()->create('Magento\App\Config\ValueInterface');
$configData->setPath('carriers/flatrate/active')
    ->setScope(\Magento\BaseScopeInterface::SCOPE_DEFAULT)
    ->setScopeId(0)
    ->setValue(1)
    ->save();

$this->getObjectManager()->get('Magento\App\CacheInterface')
    ->clean(array(\Magento\App\Config::CACHE_TAG));
