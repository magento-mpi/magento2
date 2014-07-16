<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Auth;

class DeniedIframe extends \Magento\Backend\Controller\Adminhtml\Auth
{
    /**
     * Retrieve response for deniedIframeAction()
     *
     * @return string
     */
    protected function _getDeniedIframe()
    {
        return '<script type="text/javascript">parent.window.location = \'' . $this->_objectManager->get(
            'Magento\Backend\Helper\Data'
        )->getHomePageUrl() . '\';</script>';
    }

    /**
     * Denied IFrame action
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->setBody($this->_getDeniedIframe());
    }
}
