<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Theme\Controller\Adminhtml\System\Design\Wysiwyg\Files;

class DeleteFolder extends \Magento\Theme\Controller\Adminhtml\System\Design\Wysiwyg\Files
{
    /**
     * Delete folder action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $path = $this->storage->getCurrentPath();
            $this->_getStorage()->deleteDirectory($path);
        } catch (\Exception $e) {
            $result = ['error' => true, 'message' => $e->getMessage()];
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result)
            );
        }
    }
}
