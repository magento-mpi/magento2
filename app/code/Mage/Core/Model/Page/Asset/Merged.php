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
 * Iterator that aggregates one or more assets and provides a single public file with equivalent behavior
 */
class Mage_Core_Model_Page_Asset_Merged implements Iterator
{
    /**
     * @var Magento_ObjectManager
     */
    private $_objectManager;

    /**
     * @var Mage_Core_Model_Design_PackageInterface
     */
    private $_designPackage;

    /**
     * @var Mage_Core_Model_Logger
     */
    private $_logger;

    /**
     * @var Mage_Core_Model_Page_Asset_MergeableInterface[]
     */
    private $_assets;

    /**
     * @var string
     */
    private $_contentType;

    /**
     * Whether initialization has been performed or not
     *
     * @var bool
     */
    private $_isInitialized = false;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Design_PackageInterface $designPackage
     * @param Mage_Core_Model_Logger $logger
     * @param array $assets
     * @throws InvalidArgumentException
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Design_PackageInterface $designPackage,
        Mage_Core_Model_Logger $logger,
        array $assets
    ) {
        $this->_objectManager = $objectManager;
        $this->_designPackage = $designPackage;
        $this->_logger = $logger;
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
        }
        $this->_assets = $assets;
    }

    /**
     * Attempt to merge assets, falling back to original non-merged ones, if merging fails
     */
    protected function _initialize()
    {
        if (!$this->_isInitialized) {
            $this->_isInitialized = true;
            try {
                $this->_assets = array($this->_getMergedAsset($this->_assets));
            } catch (Exception $e) {
                $this->_logger->logException($e);
            }
        }
    }

    /**
     * Retrieve asset instance representing a merged file
     *
     * @param Mage_Core_Model_Page_Asset_MergeableInterface[] $assets
     * @return Mage_Core_Model_Page_Asset_AssetInterface
     */
    protected function _getMergedAsset(array $assets)
    {
        $files = array();
        foreach ($assets as $asset) {
            $files[] = $asset->getSourceFile();
        }
        return $this->_objectManager->create('Mage_Core_Model_Page_Asset_PublicFile', array(
            'file' => $this->_designPackage->mergeFiles($files, $this->_contentType),
            'contentType' => $this->_contentType,
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @return Mage_Core_Model_Page_Asset_AssetInterface
     */
    public function current()
    {
        $this->_initialize();
        return current($this->_assets);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        $this->_initialize();
        return key($this->_assets);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->_initialize();
        next($this->_assets);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->_initialize();
        reset($this->_assets);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        $this->_initialize();
        return (bool)current($this->_assets);
    }
}
