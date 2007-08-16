<?php
/**
 * Product form boolean field helper
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Boolean extends Varien_Data_Form_Element_Select
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setValues(array(
            array(
                'label' => __('No'),
                'value' => 0,
            ),
            array(
                'label' => __('Yes'),
                'value' => 1,
            ),
        ));
    }
}
