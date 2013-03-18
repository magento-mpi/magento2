<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog fulltext search model
 *
 * @category    Saas
 * @package     Saas_Search
 */
class Saas_Search_Model_Fulltext extends Mage_CatalogSearch_Model_Fulltext
{
    /**
     * Init resource model
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_init('Saas_Search_Model_Resource_Fulltext');
    }
}
