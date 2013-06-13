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
 * Page asset representing a view file
 */
class Mage_Core_Model_Page_Asset_ViewFile implements Mage_Core_Model_Page_Asset_MergeableInterface
{
    /**
     * @var Mage_Core_Model_Design_PackageInterface
     */
    private $_designPackage;

    /**
     * @var string
     */
    private $_file;

    /**
     * @var string
     */
    private $_contentType;

    /**
     * @param Mage_Core_Model_Design_PackageInterface $designPackage
     * @param string $file
     * @param string $contentType
     * @throws InvalidArgumentException
     */
    public function __construct(Mage_Core_Model_Design_PackageInterface $designPackage, $file, $contentType)
    {
        if (empty($file)) {
            throw new InvalidArgumentException("Parameter 'file' must not be empty");
        }
        $this->_designPackage = $designPackage;
        $this->_file = $file;
        $this->_contentType = $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->_designPackage->getViewFileUrl($this->_file);
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
        return $this->_designPackage->getViewFilePublicPath($this->_file);
    }
}
