<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogSearch terms flag option array
 *
 * @category   Mage
 * @package    Magento_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Terms_Grid_OptionsArray implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Catalog Helper
     *
     * @var Magento_Catalog_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Catalog_Helper_Data $catalogHelper
     */
    public function __construct(Magento_Catalog_Helper_Data $catalogHelper)
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
