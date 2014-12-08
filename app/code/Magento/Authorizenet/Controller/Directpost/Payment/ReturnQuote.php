<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorizenet\Controller\Directpost\Payment;

class ReturnQuote extends \Magento\Authorizenet\Controller\Directpost\Payment
{
    /**
     * Return customer quote by ajax
     *
     * @return void
     */
    public function execute()
    {
        $this->_returnCustomerQuote();
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(['success' => 1])
        );
    }
}
