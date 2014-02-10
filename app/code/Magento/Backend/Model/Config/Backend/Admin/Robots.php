<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config backend model for robots.txt
 */
namespace Magento\Backend\Model\Config\Backend\Admin;

class Robots extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Filesystem\Directory\Write
     */
    protected $_directory;

    /**
     * @var string
     */
    protected $_file;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\App\Filesystem $filesystem,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $storeManager,
            $config,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_directory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $this->_file = 'robots.txt';
    }

    /**
     * Return content of default robot.txt
     *
     * @return bool|string
     */
    protected function _getDefaultValue()
    {
        if ($this->_directory->isFile($this->_file)) {
            return $this->_directory->readFile($this->_file);
        }
        return false;
    }

    /**
     * Load default content from robots.txt if customer does not define own
     *
     * @return \Magento\Backend\Model\Config\Backend\Admin\Robots
     */
    protected function _afterLoad()
    {
        if (!(string)$this->getValue()) {
            $this->setValue($this->_getDefaultValue());
        }

        return parent::_afterLoad();
    }

    /**
     * Check and process robots file
     *
     * @return \Magento\Backend\Model\Config\Backend\Admin\Robots
     */
    protected function _afterSave()
    {
        if ($this->getValue()) {
            $this->_directory->writeFile($this->_file, $this->getValue());
        }

        return parent::_afterSave();
    }
}
