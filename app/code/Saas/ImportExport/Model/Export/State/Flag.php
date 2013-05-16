<?php
/**
 * Export Flag
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Export_State_Flag extends Saas_ImportExport_Model_StateFlag
{
    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'export_entity';

    /**
     * Flag timeout in seconds
     */
    const FLAG_LIFETIME = 300;

    /**
     * Save status message
     *
     * @param string $message
     */
    public function saveStatusMessage($message)
    {
        $this->_saveFlagData(array('message' => $message));
    }

    /**
     * Save export file name
     *
     * @param string $filename
     */
    public function saveExportFilename($filename)
    {
        $this->_saveFlagData(array('file' => $filename));
    }

    /**
     * Get export file name
     *
     * @return string|null
     */
    public function getExportFilename()
    {
        $flagData = $this->getFlagData();
        return $flagData && isset($flagData['file']) ? $flagData['file'] : null;
    }

    /**
     * Save data to flag
     *
     * @param array $data
     */
    protected function _saveFlagData($data)
    {
        $oldData = $this->getFlagData();
        if ($oldData) {
            $data = array_merge($oldData, $data);
        }
        $this->setFlagData($data)->save();
    }
}
