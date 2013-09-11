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
 * RMA entity Form Attribute resource model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Model\Resource\Item\Form;

class Attribute extends \Magento\Eav\Model\Resource\Form\Attribute
{
    /**
     * Initialize connection and define main table
     */
    protected function _construct()
    {
        $this->_init('magento_rma_item_form_attribute', 'attribute_id');
    }
}
