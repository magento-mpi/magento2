<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog xml renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog extends Mage_Core_Block_Template
{
    /**
     * Limit for product sorting fields to return
     */
    const PRODUCT_SORT_FIELDS_NUMBER = 3;

    /**
     * Prefix that used in specifing filters on request
     */
    const REQUEST_FILTER_PARAM_REFIX = 'filter_';

    /**
     * Prefix that used in specifing sort order params on request
     */
    const REQUEST_SORT_ORDER_PARAM_REFIX = 'order_';

    /**
     * Retrieve product sort fields as xml object
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getProductSortFeildsXmlObject()
    {
        $ordersXmlObject    = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<orders></orders>'));
        /* @var $category Mage_Catalog_Model_Category */
        $category           = Mage::getModel('Mage_Catalog_Model_Category');
        $sortOptions        = $category->getAvailableSortByOptions();
        $sortOptions        = array_slice($sortOptions, 0, self::PRODUCT_SORT_FIELDS_NUMBER);
        $defaultSort        = $category->getDefaultSortBy();
        foreach ($sortOptions as $code => $name) {
            $item = $ordersXmlObject->addChild('item');
            if ($code == $defaultSort) {
                $item->addAttribute('isDefault', 1);
            }
            $item->addChild('code', $code);
            $item->addChild('name', $ordersXmlObject->escapeXml($name));
        }

        return $ordersXmlObject;
    }
}
