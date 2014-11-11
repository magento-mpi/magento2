<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Manage Newsletter Template Controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Controller\Adminhtml;

class Template extends \Magento\Backend\App\Action
{
    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Newsletter::template');
    }

    /**
     * Set title of page
     *
     * @return void
     */
    protected function _setTitle()
    {
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Newsletter Templates'));
    }
}
