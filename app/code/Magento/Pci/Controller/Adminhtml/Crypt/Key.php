<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Encryption key changer controller
 *
 */
namespace Magento\Pci\Controller\Adminhtml\Crypt;

use Magento\Framework\App\Filesystem\DirectoryList;

class Key extends \Magento\Backend\App\Action
{
    /**
     * Check whether config.php is writable
     *
     * @return bool
     */
    protected function _checkIsConfigPhpWritable()
    {
        /** @var \Magento\Framework\App\DeploymentConfig\Writer $writer */
        $writer = $this->_objectManager->get('Magento\Framework\App\DeploymentConfig\Writer');
        if (!$writer->checkIfWritable()) {
            $this->messageManager->addError(
                __(
                    'To enable a key change this file must be writable: %1.',
                    $writer->getAbsolutePath()
                )
            );
            return false;
        }
        return true;
    }

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
