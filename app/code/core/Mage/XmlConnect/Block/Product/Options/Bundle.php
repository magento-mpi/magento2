<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle product options xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Product_Options_Bundle extends Mage_XmlConnect_Block_Product_Options
{
    /**
     * Generate bundle product options xml
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getProductOptionsXml(Mage_Catalog_Model_Product $product)
    {
        $optionsXmlObj = $this->getProductCustomOptionsXmlObject($product)->options;

        if (!$product->isSaleable()){
            return $optionsXmlObj->asXML();
        }

        /**
         * Bundle options
         */
        $product->getTypeInstance(true)->setStoreFilter($product->getStoreId(), $product);
        $optionCollection = $product->getTypeInstance(true)->getOptionsCollection($product);
        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
            $product->getTypeInstance(true)->getOptionsIds($product),
            $product
        );
        $bundleOptions = $optionCollection->appendSelections($selectionCollection, false, false);
        if (!sizeof($bundleOptions)) {
            return $optionsXmlObj->asXML();
        }

        foreach ($bundleOptions as $_option) {
            if (!$_option->getSelections()) {
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
            $optionNode->addAttribute('label', $_option->getTitle());
            if ($_option->getRequired()) {
                $optionNode->addAttribute('is_require', 1);
            }

//            $_default = $_option->getDefaultSelection();

            foreach ($_option->getSelections() as $_selection) {
                if (!$_selection->isSaleable()) {
                    continue;
                }
                $_qty = !($_selection->getSelectionQty() * 1) ? '1' : $_selection->getSelectionQty() * 1;

                $valueNode = $optionNode->addChild('value');
                $valueNode->addAttribute('code', $_selection->getSelectionId());
                $valueNode->addAttribute('label', $_selection->getName());
                if (!$_option->isMultiSelection()) {
                    if ($_selection->getSelectionCanChangeQty()) {
                        $valueNode->addAttribute('is_qty_editable', 1);
                    }
                }
                $valueNode->addAttribute('qty', $_qty);

                $price = $product->getPriceModel()->getSelectionPreFinalPrice($product, $_selection);
                $price = sprintf('%01.2f', $price);
                $valueNode->addAttribute('price', $price);
                $valueNode->addAttribute('formated_price', $this->_formatPriceString($price, $product));

//              $_selection->getIsDefault();
            }
        }

        return $optionsXmlObj->asXML();
    }
}
