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
     * @return $this
     */
    protected function _setTitle()
    {
        return $this->_title->add(__('Newsletter Templates'));
    }
}
