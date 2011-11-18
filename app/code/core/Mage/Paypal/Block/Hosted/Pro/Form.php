<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Hosted Pro link form
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Hosted_Pro_Form extends Mage_Payment_Block_Form
{
    /**
     * Internal constructor
     * Set info template for payment step
     *
    */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('hss/info.phtml');
    }
}
