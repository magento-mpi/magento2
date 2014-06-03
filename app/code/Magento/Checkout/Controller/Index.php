<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Controller;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @return void
     */
    public function indexAction()
    {
        $this->_redirect('checkout/onepage', array('_secure' => true));
    }
}
