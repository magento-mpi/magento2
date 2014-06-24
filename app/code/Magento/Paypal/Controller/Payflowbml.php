<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Paypal\Controller;

class Payflowbml extends \Magento\Framework\App\Action\Action
{
    /**
     * Action for Bill Me Later checkout button (product view and shopping cart pages)
     *
     * @return void
     */
    public function startAction()
    {
        $this->_forward('start', 'express', 'paypal', [
                'bml' => 1,
                'button' => $this->getRequest()->getParam('button')
            ]);
    }
}
