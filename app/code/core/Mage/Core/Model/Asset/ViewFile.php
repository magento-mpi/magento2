<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Asset_ViewFile
    implements Mage_Core_Model_Asset_AssetInterface, Mage_Core_Model_Asset_MergeInterface
{
    /**
     * @var Mage_Core_Model_Design_Package
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
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param string $file
     * @param string $contentType
     * @throws InvalidArgumentException
     */
    public function __construct(Mage_Core_Model_Design_Package $designPackage, $file, $contentType)
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
        return $this->_file;
    }
}
