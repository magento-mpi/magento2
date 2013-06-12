<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Paypal_Model_Boarding_Direct extends Mage_Paypal_Model_Direct
{
    /**
     * Payment method code
     * @var string
     */
    protected $_code  = Saas_Paypal_Model_Boarding_Config::METHOD_DIRECT_BOARDING;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Saas_Paypal_Model_Boarding_Pro';

    /**
     * Returns method's config object
     *
     * @return Mage_Paypal_Model_Config
     */
    public function getConfig()
    {
        return $this->_pro->getConfig();
    }
}
