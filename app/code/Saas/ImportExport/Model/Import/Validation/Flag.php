<?php
/**
 * Import validation Flag
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Import_Validation_Flag extends Mage_Core_Model_Flag
{
    /**#@+
     * States
     */
    const STATE_IN_PROGRESS = 1;
    const STATE_NOT_IN_PROGRESS = 0;
    /**#@-*/

    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'import_validation';

    /**
     * Flag timeout in seconds
     */
    const FLAG_LIFETIME = 1800;

    /**
     * Retrieve progress state. If flag is expired then change status to false.
     *
     * @return int
     */
    public function isInProgress()
    {
        $isInProgress = $this->getState() == self::STATE_IN_PROGRESS;
        if ($isInProgress && $this->_isExpire()) {
            $this->saveAsNotInProgress();
            $isInProgress = false;
        }
        return $isInProgress;
    }

    /**
     * Save progress state as 'in progress'
     *
     * @return Saas_ImportExport_Model_Import_Validation_Flag
     */
    public function saveAsInProgress()
    {
        $this->setState(self::STATE_IN_PROGRESS)
            ->save();

        return $this;
    }

    /**
     * Save progress state as 'not in progress'
     *
     * @return Saas_ImportExport_Model_Import_Validation_Flag
     */
    public function saveAsNotInProgress()
    {
        $this->setState(self::STATE_NOT_IN_PROGRESS)
            ->save();

        return $this;
    }

    /**
     * @return bool
     */
    protected function _isExpire()
    {
        return Zend_Date::now()->toValue() - strtotime($this->getLastUpdate()) > self::FLAG_LIFETIME;
    }
}
