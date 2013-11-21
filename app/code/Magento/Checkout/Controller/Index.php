<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Controller;

class Index extends \Magento\App\Action\Action
{
    function indexAction()
    {
        $this->_redirect('checkout/onepage', array('_secure'=>true));
    }
}
