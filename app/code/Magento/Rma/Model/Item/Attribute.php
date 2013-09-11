<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Item attribute model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Model\Item;

class Attribute extends \Magento\Eav\Model\Attribute
{
    /**
     * Name of the module
     */
    const MODULE_NAME = 'Magento_Rma';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_rma_item_entity_attribute';

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
        $this->_init('Magento\Rma\Model\Resource\Item\Attribute');
    }
}
