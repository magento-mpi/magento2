<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Paypal_Model_Boarding_Pro extends Mage_Paypal_Model_Pro
{
    /**
     * API model type
     *
     * @var string
     */
    protected $_apiType = 'Saas_Paypal_Model_Api_Nvp';

    /**
     * Config model type
     *
     * @var string
     */
    protected $_configType = 'Saas_Paypal_Model_Boarding_Config';
}
