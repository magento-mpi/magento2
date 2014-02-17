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
     * @param string $file
     * @param string $contentType
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\View\Url $viewUrl,
        $file,
        $contentType
    ) {
        if (empty($file)) {
            throw new \InvalidArgumentException("Parameter 'file' must not be empty");
        }
        $this->viewUrl = $viewUrl;
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
        return $this->viewUrl->getViewFilePublicPath($this->file);
    }
}
