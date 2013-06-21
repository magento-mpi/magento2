<?php
/**
 * Saas ImportExport Helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var Magento_File_Size
     */
    protected $_fileSize;

    /**
     * Cache object
     *
     * @var Mage_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Magento_File_Size $fileSize
     * @param Mage_Core_Model_CacheInterface $cache
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Magento_File_Size $fileSize,
        Mage_Core_Model_CacheInterface $cache
    ) {
        parent::__construct($context);

        $this->_fileSize = $fileSize;
        $this->_cache = $cache;
    }

    /**
     * Register shutdown function for processing PHP Fatal Errors which had occurred during specified process
     *
     * @param string|object $object
     * @param string $method
     * @throws InvalidArgumentException
     */
    public function registerShutdownFunction($object, $method)
    {
        if (!method_exists($object, $method)) {
            throw new InvalidArgumentException("The object {$object} doesn't contain a method as {$method}");
        }

        register_shutdown_function(array($object, $method));
    }

    /**
     * Maximum size of uploaded files
     *
     * @return int
     */
    public function getMaxFileSizeInMb()
    {
        return $this->_fileSize->getMaxFileSizeInMb();
    }

    /**
     * Clean page cache
     */
    public function cleanPageCache()
    {
        $this->_cache->invalidateType(Enterprise_PageCache_Model_Cache_Type::TYPE_IDENTIFIER);
        $this->_cache->invalidateType(Mage_Core_Model_Cache_Type_Block::TYPE_IDENTIFIER);
    }
}
