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
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product stock qty block for bundle product type
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Block_CatalogInventory_Stockqty_Type_Bundle extends Mage_CatalogInventory_Block_Stockqty_Abstract
{
    /**
     * Selections collection handle
     *
     * @var Mage_Bundle_Model_Mysql4_Selection_Collection
     */
    protected $_options = null;

    /**
     * Retrive bundle selections collection based on used options
     *
     * @return Mage_Bundle_Model_Mysql4_Selection_Collection
     */
    protected function _getOptions()
    {
        if (!$this->_options) {
            $this->_getProduct()->getTypeInstance(true)->setStoreFilter($this->_getProduct()->getStoreId(), $this->_getProduct());

            $optionCollection = $this->_getProduct()->getTypeInstance(true)->getOptionsCollection($this->_getProduct());

            $selectionCollection = $this->_getProduct()->getTypeInstance(true)->getSelectionsCollection(
                $this->_getProduct()->getTypeInstance(true)->getOptionsIds($this->_getProduct()),
                $this->_getProduct()
            );

            $this->_options = $optionCollection->appendSelections($selectionCollection, false, false);
        }
        return $this->_options;
    }

    /**
     * Retrieve json config to be passed to javascript object
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $options = array();
        foreach ($this->_getOptions() as $_option) {
            $_selections = $_option->getSelections();
            if (!$_selections) {
                continue;
            }
            $option = array(
                'id'         => $_option->getId(),
                'type'       => $_option->getType(),
                'isRequired' => (bool)$_option->getRequired(),
                'selections' => array(),
            );

            foreach ($_selections as $childProduct) {
                //$defaultQty = !($childProduct->getSelectionQty() * 1) ? '1' : $childProduct->getSelectionQty() * 1;
                $selection = array (
                    'productId' => $childProduct->getId(),
                    //'defaultQty' => $defaultQty,
                    'stockQty' => ($childProduct->hasStockItem() ? $childProduct->getStockItem()->getStockQty() : 0),
                );
                $option['selections'][$childProduct->getSelectionId()] = $selection;
            }
            $options[$_option->getId()] = $option;
        }

        $config = array(
            'initialStockQty' => $this->getStockQty(),
            'options'         => $options,
            'thresholdQty'    => $this->getThresholdQty(),
            'placeholderId'   => $this->getPlaceholderId(),
        );

        return Mage::helper('core')->jsonEncode($config);
    }
}
