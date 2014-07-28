<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Widget;

class Chooser extends \Magento\Backend\App\Action
{
    /**
     * Chooser Source action
     *
     * @return void
     */
    public function execute()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $massAction = $this->getRequest()->getParam('use_massaction', false);
        $productTypeId = $this->getRequest()->getParam('product_type_id', null);

        $productsGrid = $this->_view->getLayout()->createBlock(
            'Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser',
            '',
            array(
                'data' => array(
                    'id' => $uniqId,
                    'use_massaction' => $massAction,
                    'product_type_id' => $productTypeId,
                    'category_id' => $this->getRequest()->getParam('category_id')
                )
            )
        );

        $html = $productsGrid->toHtml();

        if (!$this->getRequest()->getParam('products_grid')) {
            $categoriesTree = $this->_view->getLayout()->createBlock(
                'Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser',
                '',
                array(
                    'data' => array(
                        'id' => $uniqId . 'Tree',
                        'node_click_listener' => $productsGrid->getCategoryClickListenerJs(),
                        'with_empty_node' => true
                    )
                )
            );

            $html = $this->_view->getLayout()->createBlock(
                'Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser\Container'
            )->setTreeHtml(
                $categoriesTree->toHtml()
            )->setGridHtml(
                $html
            )->toHtml();
        }

        $this->getResponse()->setBody($html);
    }
}
