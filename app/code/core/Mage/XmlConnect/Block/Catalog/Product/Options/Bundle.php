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
 * Bundle product options xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Options_Bundle extends Mage_XmlConnect_Block_Catalog_Product_Options
{
    /**
     * Generate bundle product options xml
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $isObject
     * @return string | Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getProductOptionsXml(Mage_Catalog_Model_Product $product, $isObject = false)
    {
        $xmlModel = $this->getProductCustomOptionsXmlObject($product);
        $optionsXmlObj = $xmlModel->options;

        if (!$product->isSaleable()) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }

        /**
         * Bundle options
         */
        $product->getTypeInstance()->setStoreFilter($product->getStoreId(), $product);
        $optionCollection = $product->getTypeInstance()->getOptionsCollection($product);
        $selectionCollection = $product->getTypeInstance()->getSelectionsCollection(
            $product->getTypeInstance()->getOptionsIds($product), $product
        );
        $bundleOptions = $optionCollection->appendSelections($selectionCollection, false, false);
        if (!sizeof($bundleOptions)) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }

        foreach ($bundleOptions as $_option) {
            $selections = $_option->getSelections();
            if (empty($selections)) {
                continue;
            }

            $optionNode = $optionsXmlObj->addChild('option');

            $type = parent::OPTION_TYPE_SELECT;
            if ($_option->isMultiSelection()) {
                $type = parent::OPTION_TYPE_CHECKBOX;
            }
            $code = 'bundle_option[' . $_option->getId() . ']';
            if ($type == parent::OPTION_TYPE_CHECKBOX) {
                $code .= '[]';
            }
            $optionNode->addAttribute('code', $code);
            $optionNode->addAttribute('type', $type);
            $optionNode->addAttribute('label', $optionsXmlObj->xmlentities($_option->getTitle()));
            if ($_option->getRequired()) {
                $optionNode->addAttribute('is_required', 1);
            }

            foreach ($selections as $_selection) {
                if (!$_selection->isSaleable()) {
                    continue;
                }
                $_qty = !($_selection->getSelectionQty() * 1) ? '1' : $_selection->getSelectionQty() * 1;

                $valueNode = $optionNode->addChild('value');
                $valueNode->addAttribute('code', $_selection->getSelectionId());
                $valueNode->addAttribute('label', $optionsXmlObj->xmlentities($_selection->getName()));
                if (!$_option->isMultiSelection()) {
                    if ($_selection->getSelectionCanChangeQty()) {
                        $valueNode->addAttribute('is_qty_editable', 1);
                    }
                }
                $valueNode->addAttribute('qty', $_qty);

                $price = $product->getPriceModel()->getSelectionPreFinalPrice($product, $_selection);
                $price = Mage::helper('Mage_XmlConnect_Helper_Data')->formatPriceForXml($price);
                if ((float)$price != 0.00) {
                    $valueNode->addAttribute('price', Mage::helper('Mage_XmlConnect_Helper_Data')->formatPriceForXml(
                        Mage::helper('Mage_Core_Helper_Data')->currency($price, false, false)
                    ));
                    $valueNode->addAttribute('formated_price', $this->_formatPriceString($price, $product));
                }
            }
        }

        return $isObject ? $xmlModel : $xmlModel->asNiceXml();
    }
}
