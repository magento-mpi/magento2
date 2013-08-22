<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Merge strategy representing the following: merged file is being recreated if and only if merged file does not exist
 */
class Magento_Core_Model_Page_Asset_MergeStrategy_FileExists
    implements Magento_Core_Model_Page_Asset_MergeStrategyInterface
{
    /**
     * @var Magento_Core_Model_Page_Asset_MergeStrategyInterface
     */
    private $_strategy;

    /**
     * @var Magento_Filesystem
     */
    private $_filesystem;

    /**
     * @param Magento_Core_Model_Page_Asset_MergeStrategyInterface $strategy
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(
        Magento_Core_Model_Page_Asset_MergeStrategyInterface $strategy,
        Magento_Filesystem $filesystem
    ) {
        $this->_strategy = $strategy;
        $this->_filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeFiles(array $publicFiles, $destinationFile, $contentType)
    {
        if (!$this->_filesystem->has($destinationFile)) {
            $this->_strategy->mergeFiles($publicFiles, $destinationFile, $contentType);
        }
    }
}
