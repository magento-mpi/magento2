<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_UnitPrice_Block_Catalog_Product_Helper_Form_Unit extends Magento_Data_Form_Element_Text
{
    /**
     * Constructor
     *
     * @param array $attributes
     */
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->addClass('validate-greater-than-zero');
    }
}
