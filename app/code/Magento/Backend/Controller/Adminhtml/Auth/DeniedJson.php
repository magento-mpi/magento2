<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Auth;

class DeniedJson extends \Magento\Backend\Controller\Adminhtml\Auth
{
    /**
     * Retrieve response for deniedJsonAction()
     *
     * @return string
     */
    protected function _getDeniedJson()
    {
        return $this->_objectManager->get(
            'Magento\Core\Helper\Data'
        )->jsonEncode(
            array(
                'ajaxExpired' => 1,
                'ajaxRedirect' => $this->_objectManager->get('Magento\Backend\Helper\Data')->getHomePageUrl()
            )
        );
    }

    /**
     * Denied JSON action
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->representJson($this->_getDeniedJson());
    }
}
