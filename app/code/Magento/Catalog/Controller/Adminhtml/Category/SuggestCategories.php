<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

class SuggestCategories extends \Magento\Catalog\Controller\Adminhtml\Category
{
    /**
     * Category list suggestion based on already entered symbols
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->representJson(
            $this->_view->getLayout()->createBlock(
                'Magento\Catalog\Block\Adminhtml\Category\Tree'
            )->getSuggestedCategoriesJson(
                $this->getRequest()->getParam('label_part')
            )
        );
    }
}
