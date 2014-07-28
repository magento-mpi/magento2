<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Extension controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Connect\Controller\Adminhtml\Extension;

class Custom extends \Magento\Backend\App\Action
{

    /**
     * Check is allowed access to actions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::custom');
    }
}
