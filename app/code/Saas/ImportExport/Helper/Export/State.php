<?php
/**
 * Saas export state helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @property Saas_ImportExport_Model_Export_State_Flag $_stateFlag
 */
class Saas_ImportExport_Helper_Export_State extends Saas_ImportExport_Helper_StateAbstract
{
    /**
     * @var Saas_ImportExport_Helper_Export_Config
     */
    protected $_configHelper;

    /**
     * {@inheritdoc}
     */
    public function onValidationShutdown()
    {
        $error = error_get_last();
        if ($error && isset($error['type']) && E_ERROR == $error['type'] && $this->isInProgress()) {
            $this->saveTaskAsNotified();
        }
    }

    /**
     * Save task status message
     *
     * @param string $message
     */
    public function saveTaskStatusMessage($message)
    {
        $this->_stateFlag->saveStatusMessage($message);
    }

    /**
     * Save export file name
     *
     * @param string $filename
     */
    public function saveExportFilename($filename)
    {
        $this->_stateFlag->saveExportFilename($filename);
    }

    /**
     * Get task status message
     *
     * @return string
     */
    public function getTaskStatusMessage()
    {
        $flagData = $this->_stateFlag->getFlagData();
        return $flagData && isset($flagData['message']) ? $flagData['message'] : '';
    }
}
