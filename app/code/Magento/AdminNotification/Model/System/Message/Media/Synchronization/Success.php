<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_AdminNotification_Model_System_Message_Media_Synchronization_Success
    extends Magento_AdminNotification_Model_System_Message_Media_SynchronizationAbstract
{
    /**
     * Message identity
     *
     * @var string
     */
    protected $_identity = 'MEDIA_SYNCHRONIZATION_SUCCESS';

    /**
     * Check whether
     *
     * @return bool
     */
    protected function _shouldBeDisplayed()
    {
        $state = $this->_syncFlag->getState();
        $data = $this->_syncFlag->getFlagData();
        $hasErrors = isset($data['has_errors']) && true == $data['has_errors'] ? true : false;
        return false == $hasErrors && Magento_Core_Model_File_Storage_Flag::STATE_FINISHED == $state;
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        return __('Synchronization of media storages has been completed.');
    }
}
