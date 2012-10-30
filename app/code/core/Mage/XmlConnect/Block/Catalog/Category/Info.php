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
 * Category info xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Category_Info extends Mage_XmlConnect_Block_Catalog
{
    /**
     * Produce category info xml object
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getCategoryInfoXmlObject()
    {
        $infoXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<category_info></category_info>'));
        $category   = $this->getCategory();
        if (is_object($category) && $category->getId()) {
            /**
             * @var string $title
             *
             * Copied data from "getDefaultApplicationDesignTabs()" method in "Mage_XmlConnect_Helper_Data"
             */
            $title = $this->__('Shop');
            if ($category->getParentCategory()->getLevel() > 1) {
                $title = $infoXmlObj->escapeXml($category->getParentCategory()->getName());
            }

            $infoXmlObj->addChild('parent_title', $title);
            $pId = 0;
            if ($category->getLevel() > 1) {
                $pId = $category->getParentId();
            }
            $infoXmlObj->addChild('parent_id', $pId);
        }

        return $infoXmlObj;
    }

    /**
     * Render category info xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getCategoryInfoXmlObject()->asNiceXml();
    }
}
