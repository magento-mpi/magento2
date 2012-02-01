<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Mage_LoadTest_Model_Renderer_Sales_Item_Type_Grouped extends Mage_LoadTest_Model_Renderer_Sales_Item_Type_Abstract
{

    public function prepareRequestForCart($_product)
    {
        $this->_product = $_product;
        $typeInstance = $this->_product->getTypeInstance();

        $request = array();
        $request['product'] = $this->_product->getId();
        $request['related_product'] = '';
        $groupedQtySum = 0;
        $lastAssociatedId = null;
        foreach ($typeInstance->getAssociatedProducts($this->_product) as $product) {
            if ($product->isSaleable()) {
                $groupedQty = 1;
                if ($max = $product->getStockItem()->getQty()) {
                    $groupedQty = rand(0, min(10, $max));
                }
                $groupedQtySum += $groupedQty;
                $request['super_group'][$product->getId()] = $groupedQty;
                $lastAssociatedId = $product->getId();
            }
        }
        if ($groupedQtySum == 0 && !is_null($lastAssociatedId)) {
            $request['super_group'][$lastAssociatedId]++;
        }

        return new Varien_Object($request);
    }
}


