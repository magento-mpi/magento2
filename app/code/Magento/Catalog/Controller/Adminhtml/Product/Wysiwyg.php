<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class Wysiwyg extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * WYSIWYG editor action for ajax request
     *
     * @return void
     */
    public function execute()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = $this->_objectManager->get(
            'Magento\Framework\StoreManagerInterface'
        )->getStore(
            $storeId
        )->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );

        $content = $this->_view->getLayout()->createBlock(
            'Magento\Catalog\Block\Adminhtml\Helper\Form\Wysiwyg\Content',
            '',
            array(
                'data' => array(
                    'editor_element_id' => $elementId,
                    'store_id' => $storeId,
                    'store_media_url' => $storeMediaUrl
                )
            )
        );

        $this->getResponse()->setBody($content->toHtml());
    }
}
