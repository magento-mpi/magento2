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

class Key extends \Magento\Backend\App\Action
{
    /**
     * Check whether local.xml is writeable
     *
     * @return bool
     */
    protected function _checkIsLocalXmlWriteable()
    {
        /** @var \Magento\Framework\Filesystem\Directory\Write $configDirectory */
        $configDirectory = $this->_objectManager->get(
            'Magento\Framework\App\Filesystem'
        )->getDirectoryWrite(
            \Magento\Framework\App\Filesystem::CONFIG_DIR
        );
        if (!$configDirectory->isWritable('local.xml')) {
            $this->messageManager->addError(
                __(
                    'To enable a key change this file must be writable: %1.',
                    $configDirectory->getAbsolutePath('local.xml')
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
