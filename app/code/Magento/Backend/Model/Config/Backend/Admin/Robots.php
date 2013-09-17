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
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Backend_Admin_Robots extends Magento_Core_Model_Config_Value
{
    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var string
     */
    protected $_filePath;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_filesystem = $filesystem;
        $this->_filePath = Mage::getBaseDir() . '/robots.txt';
    }


    /**
     * Return content of default robot.txt
     *
     * @return bool|string
     */
    protected function _getDefaultValue()
    {
        $file = $this->_filePath;
        if ($this->_filesystem->isFile($file)) {
            return $this->_filesystem->read($file);
        }
        return false;
    }

    /**
     * Load default content from robots.txt if customer does not define own
     *
     * @return Magento_Backend_Model_Config_Backend_Admin_Robots
     */
    protected function _afterLoad()
    {
        if (!(string) $this->getValue()) {
            $this->setValue($this->_getDefaultValue());
        }

        return parent::_afterLoad();
    }

    /**
     * Check and process robots file
     *
     * @return Magento_Backend_Model_Config_Backend_Admin_Robots
     */
    protected function _afterSave()
    {
        if ($this->getValue()) {
            $this->_filesystem->write($this->_filePath, $this->getValue());
        }

        return parent::_afterSave();
    }
}
