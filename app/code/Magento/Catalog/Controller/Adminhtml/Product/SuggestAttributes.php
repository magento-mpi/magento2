<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class SuggestAttributes extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * Search for attributes by part of attribute's label in admin store
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(
                $this->_view->getLayout()->createBlock(
                    'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes\Search'
                )->getSuggestedAttributes(
                    $this->getRequest()->getParam('label_part')
                )
            )
        );
    }
}
