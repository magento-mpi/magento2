<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Transaction Events Types Options
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Model\Resource\Report\Settlement\Options;

class TransactionEvents
    implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Paypal\Model\Report\Settlement\Row
     */
    protected $_model;

    /**
     * @param \Magento\Paypal\Model\Report\Settlement\Row $model
     */
    public function __construct(\Magento\Paypal\Model\Report\Settlement\Row $model)
    {
        $this->_model = $model;
    }

    /**
     *  Get full list of codes with their description
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_model->getTransactionEvents();
    }
}
