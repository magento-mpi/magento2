<?php
/**
 * {license_notice}
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Phoenix_Moneybookers_Model_Wlt extends Phoenix_Moneybookers_Model_Abstract
{
    /**
     * unique internal payment method identifier
     */
    protected $_code			= 'moneybookers_wlt';
    protected $_paymentMethod	= 'WLT';
    protected $_hidelogin		= '0';
}
