<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerCustomAttributes\Model\Sales;

/**
 * Customer Quote model
 *
 * @method \Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote _getResource()
 * @method \Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote getResource()
 * @method \Magento\CustomerCustomAttributes\Model\Sales\Quote setEntityId(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Quote extends AbstractSales
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote');
    }
}
