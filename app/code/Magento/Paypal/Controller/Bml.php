<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Paypal\Controller;

class Bml extends \Magento\Framework\App\Action\Action
{
    /**
     * Action for Bill Me Later checkout button (product view and shopping cart pages)
     */
    public function startAction()
    {
        $this->_forward('start', 'payflowexpress', 'paypal', [
                'bml' => 1,
                'button' => $this->getRequest()->getParam('button')
            ]);
    }
}
