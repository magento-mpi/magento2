<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Checkout_Controller_Index extends Mage_Core_Controller_Front_Action
{
    function indexAction()
    {
        $this->_redirect('checkout/onepage', array('_secure'=>true));
    }
}
