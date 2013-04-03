<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
abstract class Mage_AdminNotification_Model_System_Message_Media_SynchronizationAbstract
    implements Mage_AdminNotification_Model_System_MessageInterface
{
    /**
     * @var Mage_Core_Model_File_Storage_Flag
     */
    protected $_syncFlag;

    /**
     * @var Mage_Core_Model_Factory_Helper
     */
    protected $_helperFactory;

    /**
     * Message identity
     *
     * @var string
     */
    protected $_identity;

    /**
     * Is displayed flag
     *
     * @var bool
     */
    protected $_isDisplayed = null;

    /**
     * @param Mage_Core_Model_File_Storage $fileStorage
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     */
    public function __construct(
        Mage_Core_Model_File_Storage $fileStorage,
        Mage_Core_Model_Factory_Helper $helperFactory
    ) {
        $this->_syncFlag = $fileStorage->getSyncFlag();
        $this->_helperFactory = $helperFactory;
    }

    /**
     * Check if message should be displayed
     *
     * @return bool
     */
    protected abstract function _shouldBeDisplayed();

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        if (null === $this->_isDisplayed) {
            $output = $this->_shouldBeDisplayed();
            if ($output) {
                $this->_syncFlag->setState(Mage_Core_Model_File_Storage_Flag::STATE_NOTIFIED);
                $this->_syncFlag->save();
            }
            $this->_isDisplayed = $output;
        }
        return $this->_isDisplayed;
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return Mage_Backend_Model_System_MessageInterface::SEVERITY_MAJOR;
    }
}