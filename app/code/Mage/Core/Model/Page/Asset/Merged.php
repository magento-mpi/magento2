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
 * Composite asset that aggregates one or more assets and provides a single public file with equivalent behavior
 */
class Mage_Core_Model_Page_Asset_Merged implements Mage_Core_Model_Page_Asset_AssetInterface
{
    /**
     * @var array
     */
    private $_files = array();

    /**
     * @var string
     */
    private $_contentType;

    /**
     * @var string
     */
    private $_url;

    /**
     * @var Mage_Core_Model_Design_PackageInterface
     */
    private $_designPackage;

    /**
     * @var Mage_Core_Helper_Data
     */
    private $_coreHelper;

    /**
     * @var Magento_Filesystem
     */
    private $_filesystem;

    /**
     * @param Mage_Core_Model_Design_PackageInterface $designPackage
     * @param Mage_Core_Helper_Data $coreHelper
     * @param Magento_Filesystem $filesystem
     * @param array $assets
     * @throws InvalidArgumentException
     */
    public function __construct(
        Mage_Core_Model_Design_PackageInterface $designPackage,
        Mage_Core_Helper_Data $coreHelper,
        Magento_Filesystem $filesystem,
        array $assets
    ) {
        $this->_designPackage = $designPackage;
        $this->_coreHelper = $coreHelper;
        $this->_filesystem = $filesystem;
        if (!$assets) {
            throw new InvalidArgumentException('At least one asset has to be passed for merging.');
        }
        /** @var $asset Mage_Core_Model_Page_Asset_MergeableInterface */
        foreach ($assets as $asset) {
            if (!($asset instanceof Mage_Core_Model_Page_Asset_MergeableInterface)) {
                throw new InvalidArgumentException(
                    'Asset has to implement Mage_Core_Model_Page_Asset_MergeableInterface.'
                );
            }
            if (!$this->_contentType) {
                $this->_contentType = $asset->getContentType();
            } else if ($asset->getContentType() != $this->_contentType) {
                throw new InvalidArgumentException(
                    "Content type '{$asset->getContentType()}' cannot be merged with '{$this->_contentType}'."
                );
            }
            $this->_files[] = $asset->getSourceFile();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        if (!$this->_url) {
            $file = $this->_designPackage->mergeFiles($this->_files, $this->_contentType);
            $this->_url = $this->_designPackage->getPublicFileUrl($file);
            if ($this->_coreHelper->isStaticFilesSigned()) {
                $fileMTime = $this->_filesystem->getMTime($file());
                $this->_url .= '?' . $fileMTime;
            }
        }
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
