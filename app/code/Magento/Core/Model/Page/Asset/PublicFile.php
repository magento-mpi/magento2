<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page asset representing a local file that already has public access
 */
namespace Magento\Core\Model\Page\Asset;

class PublicFile implements \Magento\Core\Model\Page\Asset\LocalInterface
{
    /**
     * @var \Magento\Core\Model\View\Url
     */
    protected $_viewUrl;

    /**
     * @var string
     */
    private $_file;

    /**
     * @var string
     */
    private $_contentType;

    /**
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param string $file
     * @param string $contentType
     */
    public function __construct(\Magento\Core\Model\View\Url $viewUrl, $file, $contentType)
    {
        $this->_viewUrl = $viewUrl;
        $this->_file = $file;
        $this->_contentType = $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->_viewUrl->getPublicFileUrl($this->_file);
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->_contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceFile()
    {
        return $this->_file;
    }
}
