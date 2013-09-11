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
 * Page asset residing outside of the local file system
 */
namespace Magento\Core\Model\Page\Asset;

class Remote implements \Magento\Core\Model\Page\Asset\AssetInterface
{
    /**
     * @var string
     */
    private $_url;

    /**
     * @var string
     */
    private $_contentType;

    /**
     * @param string $url
     * @param string $contentType
     */
    public function __construct($url, $contentType = 'unknown')
    {
        $this->_url = $url;
        $this->_contentType = $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->_contentType;
    }
}
