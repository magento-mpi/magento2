<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Quote;

use \Magento\Framework\Model\Resource\Db\AbstractDb;

/**
 * Quote payment resource model
 */
class Payment extends AbstractDb
{
    /**
     * Serializeable field: additional_information
     *
     * @var array
     */
    protected $_serializableFields = array('additional_information' => array(null, array()));

    /**
     * @param \Magento\Framework\App\Resource $resource
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource
    ) {
        parent::__construct($resource);
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
