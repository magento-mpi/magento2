<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Contact\Controller\Index;

class Index extends \Magento\Contact\Controller\Index
{
    /**
     * Show Contact Us page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()
            ->getBlock('contactForm')
            ->setFormAction($this->_url->getUrl('*/*/post', ['_secure' => true]));
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
