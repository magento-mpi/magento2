<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Vladimir
 * Date: 27/09/12
 * Time: 19:36
 * To change this template use File | Settings | File Templates.
 */
class Mage_SalesArchive_Model_Order_Archive_Grid_Row_UrlGenerator extends Mage_Backend_Model_Widget_Grid_Row_UrlGenerator
{
    /**
     * Generate row url
     * @param Varien_Object $item
     * @return bool|string
     */
    public function getUrl($item)
    {
        if (Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Enterprise_SalesArchive::orders')) {
            return parent::getUrl($item);
        }
        return false;
    }
}
