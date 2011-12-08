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
 * Filters xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Filters extends Mage_XmlConnect_Block_Catalog
{
    /**
     * Render filters list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $categoryId         = $this->getRequest()->getParam('category_id', null);
        $categoryXmlObj     = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element', '<category></category>');
        $filtersCollection  = Mage::getResourceModel('Mage_XmlConnect_Model_Resource_Filter_Collection')
            ->setCategoryId($categoryId);

        $filtersXmlObj = $categoryXmlObj->addChild('filters');
        foreach ($filtersCollection->getItems() as $item) {
            if (!sizeof($item->getValues())) {
                continue;
            }
            $itemXmlObj = $filtersXmlObj->addChild('item');
            $itemXmlObj->addChild('name', $categoryXmlObj->xmlentities($item->getName()));
            $itemXmlObj->addChild('code', $categoryXmlObj->xmlentities($item->getCode()));

            $valuesXmlObj = $itemXmlObj->addChild('values');
            foreach ($item->getValues() as $value) {
                $valueXmlObj = $valuesXmlObj->addChild('value');
                $valueXmlObj->addChild('id', $categoryXmlObj->xmlentities($value->getValueString()));
                $valueXmlObj->addChild('label', $categoryXmlObj->xmlentities(strip_tags($value->getLabel())));
                $valueXmlObj->addChild('count', (int)$value->getProductsCount());
            }
        }
        $categoryXmlObj->appendChild($this->getProductSortFeildsXmlObject());

        return $categoryXmlObj->asNiceXml();
    }
}
