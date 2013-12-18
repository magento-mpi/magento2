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
namespace Magento\Sitemap\Block\Adminhtml\Grid\Renderer;

class Link extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Filesystem $filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Sitemap\Model\SitemapFactory
     */
    protected $_sitemapFactory;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Sitemap\Model\SitemapFactory $sitemapFactory
     * @param \Magento\Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Sitemap\Model\SitemapFactory $sitemapFactory,
        \Magento\Filesystem $filesystem,
        array $data = array()
    ) {
        $this->_sitemapFactory = $sitemapFactory;
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
        /** @var $sitemap \Magento\Sitemap\Model\Sitemap */
        $sitemap = $this->_sitemapFactory->create();
        $url = $this->escapeHtml($sitemap->getSitemapUrl($row->getSitemapPath(), $row->getSitemapFilename()));

        $fileName = preg_replace('/^\//', '', $row->getSitemapPath() . $row->getSitemapFilename());
        $directory = $this->_filesystem->getDirectoryRead(\Magento\Filesystem::ROOT);
        if ($directory->isFile($fileName)) {
            return sprintf('<a href="%1$s">%1$s</a>', $url);
        }

        return $url;
    }

}
