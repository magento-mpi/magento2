<?php
/**
 * Catalog fulltext search model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Search_Model_Fulltext extends Mage_CatalogSearch_Model_Fulltext
{
    /**
     * Init resource model
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_init('Saas_Search_Model_Resource_Fulltext');
    }
}
