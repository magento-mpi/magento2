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
     * @var \Magento\Filesystem $filesystem
     */
    protected $_filesystem;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param \Magento\Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        \Magento\Filesystem $filesystem,
        array $data = array()
    ) {
        $this->_filesystem = $filesystem;
        parent::__construct($context, $data);
    }

    /**
     * Prepare link to display in grid
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        /** @var $sitemap Magento_Sitemap_Model_Sitemap */
        $sitemap = Mage::getModel('Magento_Sitemap_Model_Sitemap');
        $url = $this->escapeHtml($sitemap->getSitemapUrl($row->getSitemapPath(), $row->getSitemapFilename()));

        $fileName = preg_replace('/^\//', '', $row->getSitemapPath() . $row->getSitemapFilename());
        if ($this->_filesystem->isFile(BP . DS . $fileName)) {
            return sprintf('<a href="%1$s">%1$s</a>', $url);
        }

        return $url;
    }

}
