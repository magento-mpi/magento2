<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogSearch terms flag option array
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Terms_Grid_OptionsArray implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Catalog Helper
     *
     * @var Mage_Catalog_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Catalog_Helper_Data $catalogHelper
     */
    public function __construct(Mage_Catalog_Helper_Data $catalogHelper)
    {
        $this->_helper = $catalogHelper;
    }

    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => $this->_helper->__('Yes'),
            '0' => $this->_helper->__('No'),
        );
    }
}
