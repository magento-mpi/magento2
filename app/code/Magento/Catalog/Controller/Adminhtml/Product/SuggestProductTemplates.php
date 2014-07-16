<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class SuggestProductTemplates extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * Action for product template selector
     *
     * @return void
     */
    public function execute()
    {
        $this->productBuilder->build($this->getRequest());
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(
                $this->_view->getLayout()->createBlock('Magento\Catalog\Block\Product\TemplateSelector')
                    ->getSuggestedTemplates($this->getRequest()->getParam('label_part'))
            )
        );
    }
}
