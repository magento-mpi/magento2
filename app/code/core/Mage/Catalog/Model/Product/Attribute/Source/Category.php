<?php
/**
 * Product attribute category values source
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product_Attribute_Source_Category extends Mage_Catalog_Model_Product_Attribute_Source
{
    public function getArrOptions()
    {
        // TODO: use website root
        $options = Mage::getModel('catalog_resource/category_tree')
            ->joinAttribute('name')
            ->load(1,5)
            ->getNodes();
        $arr = array();
        foreach ($options as $option) {
            $arr[] = array(
                'value' => $option->getId(),
                'label' => str_repeat('&nbsp;&nbsp;&nbsp;', $option->getLevel()-1).$option->getName(),
            );
        }
        return $arr;
    }
}