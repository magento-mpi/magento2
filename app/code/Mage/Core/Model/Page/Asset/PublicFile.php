<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page asset representing a local file that already has public access
 */
class Mage_Core_Model_Page_Asset_PublicFile implements Mage_Core_Model_Page_Asset_LocalInterface
{
    /**
     * @var Mage_Core_Model_View_Url
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
     * @param Mage_Core_Model_View_Url $viewUrl
     * @param string $file
     * @param string $contentType
     */
    public function __construct(Mage_Core_Model_View_Url $viewUrl, $file, $contentType)
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
