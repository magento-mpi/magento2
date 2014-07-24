<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

class Move extends \Magento\Catalog\Controller\Adminhtml\Category
{
    /**
     * Move category action
     *
     * @return void
     */
    public function execute()
    {
        $category = $this->_initCategory();
        if (!$category) {
            $this->getResponse()->setBody(__('There was a category move error.'));
            return;
        }
        /**
         * New parent category identifier
         */
        $parentNodeId = $this->getRequest()->getPost('pid', false);
        /**
         * Category id after which we have put our category
         */
        $prevNodeId = $this->getRequest()->getPost('aid', false);

        try {
            $category->move($parentNodeId, $prevNodeId);
            $this->getResponse()->setBody('SUCCESS');
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        } catch (\Exception $e) {
            $this->getResponse()->setBody(__('There was a category move error %1', $e));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
    }
}
