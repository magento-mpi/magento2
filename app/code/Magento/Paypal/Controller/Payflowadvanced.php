<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payflow Advanced Checkout Controller
 */
namespace Magento\Paypal\Controller;

class Payflowadvanced extends \Magento\Paypal\Controller\Payflow
{
    /**
     * Redirect block name
     * @var string
     */
    protected $_redirectBlockName = 'payflow.advanced.iframe';

    /**
     * Submit transaction to Payflow getaway into iframe
     */
    public function formAction()
    {
        parent::formAction();
        $html = $this->_view->getLayout()->getBlock($this->_redirectBlockName)->toHtml();
        $this->getResponse()->setBody($html);
    }
}
