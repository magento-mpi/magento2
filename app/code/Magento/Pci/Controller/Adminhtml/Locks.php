<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Locked administrators controller
 *
 */
namespace Magento\Pci\Controller\Adminhtml;

class Locks extends \Magento\Backend\App\Action
{
    /**
     * Check whether access is allowed for current admin session
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Pci::locks');
    }
}
