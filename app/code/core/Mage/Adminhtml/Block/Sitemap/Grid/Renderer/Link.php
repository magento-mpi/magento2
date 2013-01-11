<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sitemap grid link column renderer
 *
 * @category   Mage
 * @package    Mage_Sitemap
 */
class Mage_Adminhtml_Block_Sitemap_Grid_Renderer_Link extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /** @var Magento_Filesystem */
    protected $_filesystem;

    /**
     * Constructor
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Magento_Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Magento_Filesystem $filesystem,
        array $data = array()
    ) {
        $this->_filesystem = $filesystem;
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data);
    }

    /**
     * Prepare link to display in grid
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        /** @var $sitemap Mage_Sitemap_Model_Sitemap */
        $sitemap = Mage::getModel('Mage_Sitemap_Model_Sitemap');
        $url = $this->escapeHtml($sitemap->getSitemapUrl($row->getSitemapPath(), $row->getSitemapFilename()));

        $fileName = preg_replace('/^\//', '', $row->getSitemapPath() . $row->getSitemapFilename());
        if ($this->_filesystem->isFile(Mage::getBaseDir() . DS . $fileName, Mage::getBaseDir())) {
            return sprintf('<a href="%1$s">%1$s</a>', $url);
        }

        return $url;
    }

}
