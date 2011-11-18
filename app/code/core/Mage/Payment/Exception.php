<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Payment exception
 *
 * @category   Mage
 * @package    Mage_Payment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Payment_Exception extends Exception
{
    protected $_code = null;

    public function __construct($message = null, $code = 0)
    {
        $this->_code = $code;
        parent::__construct($message, 0);
    }

    public function getFields()
    {
        return $this->_code;
    }
}
