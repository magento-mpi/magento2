<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Model\Sales;

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
