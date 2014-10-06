<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class Edit extends \Magento\Catalog\Controller\Adminhtml\Product
{

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = array('edit');

    /**
     * Product edit form
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Products'));
        $productId = (int) $this->getRequest()->getParam('id');
        $product = $this->productBuilder->build($this->getRequest());

        if ($productId && !$product->getId()) {
            $this->messageManager->addError(__('This product no longer exists.'));
            $this->_redirect('catalog/*/');
            return;
        }

        $this->_title->add($product->getName());

        $this->_eventManager->dispatch('catalog_product_edit_action', array('product' => $product));

        $this->_view->loadLayout(
            array(
                'default',
                strtolower($this->_request->getFullActionName()),
                'catalog_product_' . $product->getTypeId()
            )
        );

        $this->_setActiveMenu('Magento_Catalog::catalog_products');

        if (!$this->_objectManager->get(
            'Magento\Framework\StoreManagerInterface'
        )->isSingleStoreMode() && ($switchBlock = $this->_view->getLayout()->getBlock(
            'store_switcher'
        ))
        ) {
            $switchBlock->setDefaultStoreName(__('Default Values'))
                ->setWebsiteIds($product->getWebsiteIds())
                ->setSwitchUrl(
                    $this->getUrl(
                        'catalog/*/*',
                        array('_current' => true, 'active_tab' => null, 'tab' => null, 'store' => null)
                    )
                );
        }

        $block = $this->_view->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }

        $this->_view->renderLayout();
    }
}
