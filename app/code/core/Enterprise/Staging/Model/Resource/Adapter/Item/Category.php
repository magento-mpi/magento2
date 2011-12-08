<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adapter item category resource
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Adapter_Item_Category
    extends Enterprise_Staging_Model_Resource_Adapter_Item_Default
{
    /**
     * List of table codes which shouldn't process if product item were not chosen
     *
     * @var array
     */
    protected $_ignoreIfProductNotChosen     = array('category_product', 'category_product_index');

    /**
     * Create item table and records, run processes in website and store scopes
     *
     * @param string $entityName
     * @return Enterprise_Staging_Model_Resource_Adapter_Item_Category
     */
    protected function _createItem($entityName)
    {
        if (!$this->getStaging()->getMapperInstance()->hasStagingItem('product')) {
            if (strpos($entityName, 'product') !== false) {
                return $this;
            }
        }
        return parent::_createItem($entityName);
    }
}
