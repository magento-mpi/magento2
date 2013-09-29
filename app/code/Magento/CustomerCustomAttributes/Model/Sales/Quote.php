<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Quote model
 *
 * @method \Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote _getResource()
 * @method \Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote getResource()
 * @method \Magento\CustomerCustomAttributes\Model\Sales\Quote setEntityId(int $value)
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerCustomAttributes\Model\Sales;

class Quote extends \Magento\CustomerCustomAttributes\Model\Sales\AbstractSales
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\CustomerCustomAttributes\Model\Resource\Sales\Quote');
    }
}
