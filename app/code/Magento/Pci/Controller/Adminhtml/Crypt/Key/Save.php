<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pci\Controller\Adminhtml\Crypt\Key;

class Save extends \Magento\Pci\Controller\Adminhtml\Crypt\Key
{
    /**
     * Process saving new encryption key
     *
     * @return void
     */
    public function execute()
    {
        try {
            $key = null;

            if (0 == $this->getRequest()->getPost('generate_random')) {
                $key = $this->getRequest()->getPost('crypt_key');
                if (empty($key)) {
                    throw new \Exception(__('Please enter an encryption key.'));
                }
                $this->_objectManager->get('Magento\Framework\Encryption\EncryptorInterface')->validateKey($key);
            }

            $newKey = $this->_objectManager->get('Magento\Pci\Model\Resource\Key\Change')->changeEncryptionKey($key);
            $this->messageManager->addSuccess(__('The encryption key has been changed.'));

            if (!$key) {
                $this->messageManager->addNotice(
                    __(
                        'This is your new encryption key: <span style="font-family:monospace;">%1</span>. Be sure to write it down and take good care of it!',
                        $newKey
                    )
                );
            }
            $this->_objectManager->get('Magento\Framework\App\CacheInterface')->clean();
        } catch (\Exception $e) {
            if ($message = $e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(['crypt_key' => $key]);
        }
        $this->_redirect('adminhtml/*/');
    }
}
