<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml backend model for robots.txt
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Admin_Robots extends Mage_Core_Model_Config_Data
{
    /**
     * Return content of default robot.txt
     *
     * @return bool|string
     */
    protected function _getDefaultValue()
    {
        $fileIo = $this->_getFileObject();
        $file = $this->_getRobotsTxtFilePath();
        if ($fileIo->fileExists($file)) {
            $fileIo->open(array('path' => $fileIo->getDestinationFolder($file)));
            return $fileIo->read($file);
        }
        return false;
    }

    /**
     * Load default content from robots.txt if customer does not define own
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_Admin_Robots
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
     * @return Mage_Adminhtml_Model_System_Config_Backend_Admin_Robots
     */
    protected function _afterSave()
    {
        if ($this->getValue()) {
            $file = $this->_getRobotsTxtFilePath();
            $fileIo = $this->_getFileObject();
            $fileIo->open(array('path' => $fileIo->getDestinationFolder($file)));
            $fileIo->write($file, $this->getValue());
        }

        return parent::_afterSave();
    }

    /**
     * Get path to robots.txt
     *
     * @return string
     */
    protected function _getRobotsTxtFilePath()
    {
        return $this->_getFileObject()->getCleanPath(Mage::getBaseDir() . DS . 'robots.txt');
    }

    /**
     * Get file io
     *
     * @return Varien_Io_File
     */
    protected function _getFileObject()
    {
        return new Varien_Io_File();
    }
}
