<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ogone\Controller\Api;

class Exception extends \Magento\Ogone\Controller\Api
{
    /**
     * The payment result is uncertain
     * exception status can be 52 or 92
     * need to change order status as processing Ogone
     * update transaction id
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_validateOgoneData()) {
            $this->_redirect('checkout/cart');
            return;
        }
        $this->_exceptionProcess();
    }
}
