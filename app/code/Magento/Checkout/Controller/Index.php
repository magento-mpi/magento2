<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Checkout_Controller_Index extends Magento_Core_Controller_Front_Action
{
    function indexAction()
    {
        $this->_redirect('checkout/onepage', array('_secure'=>true));
    }
}
