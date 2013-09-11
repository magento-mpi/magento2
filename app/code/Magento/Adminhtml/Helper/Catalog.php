<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Catalog helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Helper;

class Catalog extends \Magento\Core\Helper\AbstractHelper
{
    const XML_PATH_SITEMAP_VALID_PATHS = 'general/file/sitemap_generate_valid_paths';

    /**
     * Attribute Tab block name for product edit
     *
     * @var string
     */
    protected $_attributeTabBlock = null;

    /**
     * Attribute Tab block name for category edit
     *
     * @var string
     */
    protected $_categoryAttributeTabBlock;

    /**
     * Retrieve Attribute Tab Block Name for Product Edit
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        return $this->_attributeTabBlock;
    }

    /**
     * Set Custom Attribute Tab Block Name for Product Edit
     *
     * @param string $attributeTabBlock
     * @return \Magento\Adminhtml\Helper\Catalog
     */
    public function setAttributeTabBlock($attributeTabBlock)
    {
        $this->_attributeTabBlock = $attributeTabBlock;
        return $this;
    }

    /**
     * Retrieve Attribute Tab Block Name for Category Edit
     *
     * @return string
     */
    public function getCategoryAttributeTabBlock()
    {
        return $this->_categoryAttributeTabBlock;
    }

    /**
     * Set Custom Attribute Tab Block Name for Category Edit
     *
     * @param string $attributeTabBlock
     * @return \Magento\Adminhtml\Helper\Catalog
     */
    public function setCategoryAttributeTabBlock($attributeTabBlock)
    {
        $this->_categoryAttributeTabBlock = $attributeTabBlock;
        return $this;
    }

    /**
     * Get list valid paths for generate a sitemap XML file
     *
     * @return array
     */
    public function getSitemapValidPaths()
    {
        $path = \Mage::getStoreConfig(self::XML_PATH_SITEMAP_VALID_PATHS);
        /** @var $helper \Magento\Core\Helper\Data */
        $helper = \Mage::helper('Magento\Core\Helper\Data');
        $path = array_merge($path, $helper->getPublicFilesValidPath());
        return $path;
    }
}
