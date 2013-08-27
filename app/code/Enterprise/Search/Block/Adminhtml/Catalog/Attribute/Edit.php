<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Enterprise attribute edit block
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Block_Adminhtml_Catalog_Attribute_Edit extends Magento_Adminhtml_Block_Template
{
    /**
     * Return true if third part search engine used
     *
     * @return boolean
     */
    public function isThirdPartSearchEngine()
    {
        return Mage::helper('Enterprise_Search_Helper_Data')->isThirdPartSearchEngine();
    }
}
