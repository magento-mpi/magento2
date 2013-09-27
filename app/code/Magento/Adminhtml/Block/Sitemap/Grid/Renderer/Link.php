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
 * Sitemap grid link column renderer
 *
 * @category   Magento
 * @package    Magento_Sitemap
 */
class Magento_Adminhtml_Block_Sitemap_Grid_Renderer_Link extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @var Magento_Filesystem $filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_Sitemap_Model_SitemapFactory
     */
    protected $_sitemapFactory;

    /**
     * @param Magento_Sitemap_Model_SitemapFactory $sitemapFactory
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        Magento_Sitemap_Model_SitemapFactory $sitemapFactory,
        Magento_Backend_Block_Context $context,
        Magento_Filesystem $filesystem,
        array $data = array()
    ) {
        $this->_sitemapFactory = $sitemapFactory;
        $this->_filesystem = $filesystem;
        parent::__construct($context, $data);
    }

    /**
     * Prepare link to display in grid
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        /** @var $sitemap Magento_Sitemap_Model_Sitemap */
        $sitemap = $this->_sitemapFactory->create();
        $url = $this->escapeHtml($sitemap->getSitemapUrl($row->getSitemapPath(), $row->getSitemapFilename()));

        $fileName = preg_replace('/^\//', '', $row->getSitemapPath() . $row->getSitemapFilename());
        if ($this->_filesystem->isFile(BP . DS . $fileName)) {
            return sprintf('<a href="%1$s">%1$s</a>', $url);
        }

        return $url;
    }

}
