<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_AdminNotification_Model_System_Message_Media_Synchronization_Error
    extends Magento_AdminNotification_Model_System_Message_Media_SynchronizationAbstract
{
    /**
     * Message identity
     *
     * @var string
     */
    protected $_identity = 'MEDIA_SYNCHRONIZATION_ERROR';

    /**
     * Check whether
     *
     * @return bool
     */
    protected function _shouldBeDisplayed()
    {
        $data = $this->_syncFlag->getFlagData();
        return isset($data['has_errors']) && true == $data['has_errors'];
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        return __('One or more media files failed to be synchronized during the media storages synchronization process. Refer to the log file for details.');
    }
}
