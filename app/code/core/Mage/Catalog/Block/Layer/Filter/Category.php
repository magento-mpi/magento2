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
 * Catalog layer category filter
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Mage_Catalog_Model_Layer_Filter_Category';
    }
}
