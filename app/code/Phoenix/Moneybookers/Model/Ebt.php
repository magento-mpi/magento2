<?php
/**
 * {license_notice}
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Phoenix_Moneybookers_Model_Ebt extends Phoenix_Moneybookers_Model_Abstract
{
    /**
     * unique internal payment method identifier
     */
    protected $_code			= 'moneybookers_ebt';
    protected $_paymentMethod	= 'EBT';
}
