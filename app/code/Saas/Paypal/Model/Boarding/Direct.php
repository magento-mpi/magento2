<?php
/**
 * Magento Saas Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
