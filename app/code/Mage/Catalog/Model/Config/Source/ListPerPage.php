<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog products per page on List mode source
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Config_Source_ListPerPage implements Magento_Core_Model_Option_ArrayInterface
{

    public function toOptionArray()
    {
        $result = array();
        $perPageValues = Mage::getConfig()->getNode('frontend/catalog/per_page_values/list');
        $perPageValues = explode(',', $perPageValues);
        foreach ($perPageValues as $option) {
            $result[] = array('value' => $option, 'label' => $option);
        }
        return $result;
    }

}
