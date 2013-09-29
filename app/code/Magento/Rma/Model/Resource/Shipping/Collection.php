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
 * RMA shipping collection
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Model\Resource\Shipping;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('Magento\Rma\Model\Shipping', 'Magento\Rma\Model\Resource\Shipping');
    }
}
