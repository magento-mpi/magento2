<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Related products block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Related extends Mage_XmlConnect_Block_Catalog_Product_List
{
    /**
     * Retrieve related products xml object based on current product
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     * @see $this->getProduct()
     */
    public function getRelatedProductsXmlObj()
    {
        /** @var $relatedXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $relatedXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<related_products></related_products>'));

        $productObj = $this->getParentBlock()->getProduct();

        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_TargetRule'))) {
            Mage::register('product', $productObj);

            $productBlock = $this->getLayout()->addBlock(
                'Enterprise_TargetRule_Block_Catalog_Product_List_Related', 'relatedProducts'
            );

            $collection = $productBlock->getItemCollection();
        } else {
            if ($productObj->getId() > 0) {
                $collection = $this->_getProductCollection();
                if (!$collection) {
                    return $relatedXmlObj;
                }
                $collection = $collection->getItems();
            }
        }

        $this->_addProductXmlObj($relatedXmlObj, $collection);

        return $relatedXmlObj;
    }

    /**
     * Add related products info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $relatedXmlObj
     * @param array $collection
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _addProductXmlObj(Mage_XmlConnect_Model_Simplexml_Element $relatedXmlObj, $collection)
    {
        foreach ($collection as $product) {
            $productXmlObj = $this->productToXmlObject($product);

            if (!$productXmlObj) {
                continue;
            }

            if ($this->getParentBlock()->getChildBlock('product_price')) {
                $this->getParentBlock()->getChildBlock('product_price')->setProduct($product)
                    ->setProductXmlObj($productXmlObj)->collectProductPrices();
            }
            $relatedXmlObj->appendChild($productXmlObj);
        }
        return $relatedXmlObj;
    }

    /**
     * Generate related products xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getRelatedProductsXmlObj()->asNiceXml();
    }

    /**
     * Retrieve product collection with all prepared data
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = $this->getParentBlock()->getProduct()->getRelatedProductCollection();
            Mage::getSingleton('Mage_Catalog_Model_Layer')->prepareProductCollection($collection);
            /**
             * Add rating and review summary, image attribute, apply sort params
             */
            $this->_prepareCollection($collection);
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}
