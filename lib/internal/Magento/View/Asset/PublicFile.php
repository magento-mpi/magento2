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
     * @var \Magento\View\UrlResolver
     */
    protected $resolver;

    /**
     * File
     *
     * @var string
     */
    protected $file;

    /**
     * Content type
     * @var string
     */
    protected $contentType;

    /**
     * @param \Magento\View\UrlResolver $source
     * @param string $file
     * @param string $contentType
     */
    public function __construct(\Magento\View\UrlResolver $source, $file, $contentType)
    {
        $this->resolver = $source;
        $this->file = $file;
        $this->contentType = $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->resolver->getPublicFileUrl($this->file);
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
