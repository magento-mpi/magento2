<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Quote;

/**
 * Quote payment resource model
 */
class Payment extends \Magento\Sales\Model\Resource\AbstractResource
{
    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields = array('additional_information' => array(null, array()));

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Stdlib\DateTime $dateTime
    ) {
        parent::__construct($resource, $dateTime);
    }

    /**
     * Main table and field initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_quote_payment', 'payment_id');
    }
}
