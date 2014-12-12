<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Model\Resource\Item\Form;

/**
 * RMA entity Form Attribute resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Attribute extends \Magento\Eav\Model\Resource\Form\Attribute
{
    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_rma_item_form_attribute', 'attribute_id');
    }
}
