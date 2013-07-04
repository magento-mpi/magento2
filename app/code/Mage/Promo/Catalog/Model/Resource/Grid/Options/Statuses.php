<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Promo
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Promo_Catalog_Model_Resource_Grid_Options_Statuses implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_Index_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Index_Helper_Data $helper
     */
    public function __construct(Mage_Index_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Return options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '0' => $this->_helper->__('Inactive'),
            '1' => $this->_helper->__('Active'),
        );
    }
}