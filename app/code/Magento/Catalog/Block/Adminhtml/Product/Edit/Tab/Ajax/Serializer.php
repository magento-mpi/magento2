<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Ajax;

class Serializer extends \Magento\Framework\View\Element\Template
{
    /**
     * @return $this
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('catalog/product/edit/serializer.phtml');
        return $this;
    }

    /**
     * @return string
     */
    public function getProductsJSON()
    {
        $result = array();
        if ($this->getProducts()) {
            $isEntityId = $this->getIsEntityId();
            foreach ($this->getProducts() as $product) {
                $id = $isEntityId ? $product->getEntityId() : $product->getId();
                $result[$id] = $product->toArray(array('qty', 'position'));
            }
        }
        return $result ? \Zend_Json::encode($result) : '{}';
    }
}
