<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stub abstract class which provide to change protected property "$_disabledAttrs" and test methods depended on it
 */
namespace Magento\ImportExport\Model\Export;

abstract class AbstractStubEntity extends \Magento\ImportExport\Model\Export\AbstractEntity
{
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\ImportExport\Model\Export\Factory $collectionFactory,
        \Magento\ImportExport\Model\Resource\CollectionByPagesIteratorFactory $resourceColFactory,
        array $data = array()
    ) {
        parent::__construct($scopeConfig, $storeManager, $collectionFactory, $resourceColFactory, $data);
        $this->_disabledAttrs = array('default_billing', 'default_shipping');
    }
}
