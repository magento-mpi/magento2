<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Controller\Adminhtml\Product;

class JsonProductInfo extends \Magento\Review\Controller\Adminhtml\Product
{
    /**
     * @return void
     */
    public function execute()
    {
        $response = new \Magento\Framework\Object();
        $id = $this->getRequest()->getParam('id');
        if (intval($id) > 0) {
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($id);

            $response->setId($id);
            $response->addData($product->getData());
            $response->setError(0);
        } else {
            $response->setError(1);
            $response->setMessage(__('We can\'t get the product ID.'));
        }
        $this->getResponse()->representJson($response->toJSON());
    }
}
