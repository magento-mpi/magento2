<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * Page asset representing a view file
 */
class ViewFile implements MergeableInterface
{
    /**
     * View URL
     *
     * @var \Magento\View\Url
     */
    protected $viewUrl;

    /**
     * @var \Magento\View\FileResolver
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
     *
     * @var string
     */
    protected $contentType;

    /**
     * Constructor
     *
     * @param \Magento\View\Url $viewUrl
     * @param \Magento\View\FileResolver $source
     * @param string $file
     * @param string $contentType
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\View\Url $viewUrl,
        \Magento\View\FileResolver $source,
        $file,
        $contentType
    ) {
        if (empty($file)) {
            throw new \InvalidArgumentException("Parameter 'file' must not be empty");
        }
        $this->viewUrl = $viewUrl;
        $this->resolver = $source;
        $this->file = $file;
        $this->contentType = $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->viewUrl->getViewFileUrl($this->file);
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
        return $this->resolver->getPublicViewFile($this->file);
    }
}
