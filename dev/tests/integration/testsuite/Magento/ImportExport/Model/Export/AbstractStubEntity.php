<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stub abstract class which provide to change protected property "$_disabledAttrs" and test methods depended on it
 */
namespace Magento\ImportExport\Model\Export;

abstract class AbstractStubEntity
    extends \Magento\ImportExport\Model\Export\AbstractEntity
{
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\ImportExport\Model\Export\Factory $collectionFactory,
        \Magento\ImportExport\Model\Resource\CollectionByPagesIteratorFactory $resourceColFactory,
        array $data = array()
    ) {
        parent::__construct($coreStoreConfig, $storeManager, $collectionFactory, $resourceColFactory, $data);
        $this->_disabledAttrs = array('default_billing', 'default_shipping');
    }
}
