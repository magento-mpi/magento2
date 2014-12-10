<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Encryption key changer controller
 *
 */
namespace Magento\Pci\Controller\Adminhtml\Crypt;

class Key extends \Magento\Backend\App\Action
{
    /**
     * Check whether current administrator session allows this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Pci::crypt_key');
    }
}
