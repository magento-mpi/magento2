<?php
/**
 * Filesystem decorator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Cdn_Model_Filesystem extends Magento_Filesystem
{
    /**
     * Store object that uses for manipulate Magento's directories
     *
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * List of dirs that uses to store CDN content (images and etc.)
     *
     * @var array()
     */
    protected $_trackedDirs;

    /**
     * Used to manipulate files on CDN
     *
     * @var Saas_Cdn_Model_CdnInterface
     */
    protected $_cdn;

    /**
     * Initialize filesystem model data
     *
     * @param Magento_Filesystem_AdapterInterface $adapter
     * @param Mage_Core_Model_Dir $dirs
     * @param Saas_Cdn_Model_CdnInterface $cdn
     */
    public function __construct(
        Magento_Filesystem_AdapterInterface $adapter,
        Mage_Core_Model_Dir $dirs,
        Saas_Cdn_Model_CdnInterface $cdn
    )
    {
        $this->_cdn = $cdn;
        $this->_dirs = $dirs;
        $this->_trackedDirs[] = $dirs->getDir(Mage_Core_Model_Dir::MEDIA);
        parent::__construct($adapter);
    }

    /**
     * @param string $key
     * @param null $workingDirectory
     * @return bool
     */
    public function delete($key, $workingDirectory = null)
    {
        $result = parent::delete($key, $workingDirectory);
        foreach ($this->_trackedDirs as $dir) {
            if (0 === strpos($key, $dir)) {
                $this->_cdn->deleteFile($key);
                break;
            }
        }
        return $result;
    }
}
