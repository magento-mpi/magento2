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
 * Filter collection
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_Filter_Collection extends Varien_Data_Collection
{
    /**
     * Set CategoryId filter
     *
     * @param int $categoryId
     * @return Mage_XmlConnect_Model_Resource_Filter_Collection
     */
    public function setCategoryId($categoryId)
    {
        if ((int)$categoryId > 0) {
            $this->addFilter('category_id', $categoryId);
        }
        return $this;
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_XmlConnect_Model_Resource_Filter_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if (empty($this->_items)) {
            $layer = Mage::getSingleton('Mage_Catalog_Model_Layer');
            foreach ($this->_filters as $filter) {
                if ('category_id' == $filter['field']) {
                    $layer->setCurrentCategory((int)$filter['value']);
                }
            }
            if ($layer->getCurrentCategory()->getIsAnchor()) {
                foreach ($layer->getFilterableAttributes() as $attributeItem) {
                    $filterModelName = 'Mage_Catalog_Model_Layer_Filter_Attribute';
                    switch ($attributeItem->getAttributeCode()) {
                        case 'price':
                            $filterModelName = 'Mage_Catalog_Model_Layer_Filter_Price';
                            break;
                        case 'decimal':
                            $filterModelName = 'Mage_Catalog_Model_Layer_Filter_Decimal';
                            break;
                    }

                    $filterModel = Mage::getModel($filterModelName);
                    $filterModel->setLayer($layer)->setAttributeModel($attributeItem);
                    $filterValues = new Varien_Data_Collection;
                    foreach ($filterModel->getItems() as $valueItem) {
                        $valueObject = new Varien_Object();
                        $valueObject->setLabel($valueItem->getLabel());
                        $valueObject->setValueString($valueItem->getValueString());
                        $valueObject->setProductsCount($valueItem->getCount());
                        $filterValues->addItem($valueObject);
                    }
                    $item = new Varien_Object;
                    $item->setCode($attributeItem->getAttributeCode());
                    $item->setName($filterModel->getName());
                    $item->setValues($filterValues);
                    $this->addItem($item);
                }
            }
        }
        return $this;
    }
}
