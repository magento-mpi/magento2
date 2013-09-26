<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quote payment resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Quote;

class Payment extends \Magento\Sales\Model\Resource\AbstractResource
{
    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields   = array(
        'additional_information' => array(null, array())
    );

    /**
     * @var \Magento\Sales\Model\Payment\Method\Converter
     */
    protected $_paymentConverter;

    /**
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Sales\Model\Payment\Method\Converter $paymentConverter
     */
    public function __construct(
        \Magento\Core\Model\Resource $resource,
        \Magento\Sales\Model\Payment\Method\Converter $paymentConverter
    ) {
        $this->_paymentConverter = $paymentConverter;
        parent::__construct($resource);
    }

    /**
     * Main table and field initialization
     */
    protected function _construct()
    {
        $this->_converter = $this->_paymentConverter;
        $this->_init('sales_flat_quote_payment', 'payment_id');
    }
}
