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
 * Product search suggestions renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Search_Suggest extends Mage_CatalogSearch_Block_Autocomplete
{
    /**
     * Suggest item separator
     */
    const SUGGEST_ITEM_SEPARATOR = '::sep::';

    /**
     * Search suggestions xml renderer
     *
     * @return string
     */
    protected function _toHtml()
    {
        $suggestXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<suggestions></suggestions>'));

        if (!$this->getRequest()->getParam(Mage_CatalogSearch_Helper_Data::QUERY_VAR_NAME, false)) {
            return $suggestXmlObj->asNiceXml();
        }

        $suggestData = $this->getSuggestData();
        if (!count($suggestData)) {
            return $suggestXmlObj->asNiceXml();
        }

        $items = '';
        foreach ($suggestData as $item) {
            $items .= $suggestXmlObj->escapeXml($item['title']) . self::SUGGEST_ITEM_SEPARATOR
                . (int)$item['num_of_results'] . self::SUGGEST_ITEM_SEPARATOR;
        }

        $suggestXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<suggestions>' . $items . '</suggestions>'));

        return $suggestXmlObj->asNiceXml();
    }
}
