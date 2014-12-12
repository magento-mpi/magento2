<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * RMA entity resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Model\Resource\Item;

class Attribute extends \Magento\Eav\Model\Resource\Attribute
{
    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored.
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    protected function _getEavWebsiteTable()
    {
        return $this->getTable('magento_rma_item_eav_attribute_website');
    }

    /**
     * Get Form attribute table
     *
     * Get table, where dependency between form name and attribute ids is stored.
     *
     * @return string|null
     */
    protected function _getFormAttributeTable()
    {
        return $this->getTable('magento_rma_item_form_attribute');
    }
}
