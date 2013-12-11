<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * Page asset residing outside of the local file system
 */
class Remote implements AssetInterface
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @param string $url
     * @param string $contentType
     */
    public function __construct($url, $contentType = 'unknown')
    {
        $this->url = $url;
        $this->contentType = $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->contentType;
    }
}
