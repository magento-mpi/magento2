<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Item attribute model
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Model_Item_Attribute extends Magento_Eav_Model_Attribute
{
    /**
     * Name of the module
     */
    const MODULE_NAME = 'Enterprise_Rma';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'enterprise_rma_item_entity_attribute';

    /**
     * Prefix of model events object
     *
     * @var string
     */
    protected $_eventObject = 'attribute';

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Rma_Model_Resource_Item_Attribute');
    }
}
