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
 * @var \Magento\Framework\App\Config\Value $configData
 */
$configData = $this->getObjectManager()->create('Magento\Framework\App\Config\Value');
$configData->setPath('admin/security/use_form_key')
    ->setScope(\Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT)
    ->setScopeId(0)
    ->setValue(0)
    ->save();

$this->getObjectManager()->get('Magento\Framework\App\CacheInterface')
    ->clean(array(\Magento\Framework\App\Config::CACHE_TAG));
