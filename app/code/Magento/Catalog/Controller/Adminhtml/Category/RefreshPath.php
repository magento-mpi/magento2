<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

class RefreshPath extends \Magento\Catalog\Controller\Adminhtml\Category
{
    /**
     * Build response for refresh input element 'path' in form
     *
     * @return void
     */
    public function execute()
    {
        $categoryId = (int)$this->getRequest()->getParam('id');
        if ($categoryId) {
            $category = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
            $this->getResponse()->representJson(
                $this->_objectManager->get(
                    'Magento\Core\Helper\Data'
                )->jsonEncode(
                    array('id' => $categoryId, 'path' => $category->getPath())
                )
            );
        }
    }
}
