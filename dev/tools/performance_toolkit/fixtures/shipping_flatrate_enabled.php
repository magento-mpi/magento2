<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\ToolkitFramework\Application $this */
$this->resetObjectManager();
/**
 * @var \Magento\Core\Model\Config\Value $configData
 */
$configData = $this->getObjectManager()->create('Magento\Core\Model\Config\Value');
$configData->setPath('carriers/flatrate/active')
    ->setScope(\Magento\BaseScopeInterface::SCOPE_DEFAULT)
    ->setScopeId(0)
    ->setValue(1)
    ->save();

$this->getObjectManager()->get('Magento\App\CacheInterface')
    ->clean(array(\Magento\App\Config::CACHE_TAG));
