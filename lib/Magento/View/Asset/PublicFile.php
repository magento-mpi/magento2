<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * Page asset representing a local file that already has public access
 */
class PublicFile implements LocalInterface
{
    /**
     * @var \Magento\View\Url
     */
    protected $viewUrl;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @param \Magento\View\Url $viewUrl
     * @param string $file
     * @param string $contentType
     */
    public function __construct(\Magento\View\Url $viewUrl, $file, $contentType)
    {
        $this->viewUrl = $viewUrl;
        $this->file = $file;
        $this->contentType = $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->viewUrl->getPublicFileUrl($this->file);
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceFile()
    {
        return $this->file;
    }
}
