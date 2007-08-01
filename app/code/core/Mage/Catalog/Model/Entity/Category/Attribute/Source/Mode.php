<?php
/**
 * Catalog category landing page attribute source
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Category_Attribute_Source_Mode extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'value' => 'products',
                    'label' => __('Display products only'),
                ),
                array(
                    'value' => 'page',
                    'label' => __('Display only landing page'),
                ),
                array(
                    'value' => 'products_and_page',
                    'label' => __('Display Landing Page + Products'),
                )
            );
        }
        return $this->_options;
    }
}
