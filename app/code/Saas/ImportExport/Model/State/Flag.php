<?php
/**
 * Import/Export Abstract state flag
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Saas_ImportExport_Model_State_Flag extends Mage_Core_Model_Flag
{
    /**#@+
     * State flags
     */
    const STATE_QUEUED     = 1;
    const STATE_PROCESSING = 2;
    const STATE_FINISHED   = 3;
    const STATE_NOTIFIED   = 4;
    /**#@-*/

    /**
     * Flag max lifetime after last update
     */
    const FLAG_LIFETIME = 120;

    /**
     * Load flag data
     */
    protected function _construct()
    {
        parent::_construct();
        $this->loadSelf();
    }

    /**
     * Retrieve progress state. If flag is expired then change status to false.
     *
     * @return bool
     */
    public function isInProgress()
    {
        $isInProgress = in_array($this->getState(), array(self::STATE_QUEUED, self::STATE_PROCESSING));
        if ($isInProgress && $this->_isExpire()) {
            $this->saveAsNotified();
            $isInProgress = false;
        }
        return $isInProgress;
    }

    /**
     * @return bool
     */
    public function isTaskFinished()
    {
        return $this->getState() == self::STATE_FINISHED;
    }

    /**
     * Change status to self::STATE_QUEUED and save
     */
    public function saveAsQueued()
    {
        $this->setData('flag_data', null);
        $this->setState(self::STATE_QUEUED)->save();
    }

    /**
     * Change status to self::STATE_PROCESSING and save
     */
    public function saveAsProcessing()
    {
        $this->setState(self::STATE_PROCESSING)->save();
    }

    /**
     * Change status to self::STATE_FINISHED and save
     */
    public function saveAsFinished()
    {
        $this->setState(self::STATE_FINISHED)->save();
    }

    /**
     * Change status to self::STATE_NOTIFIED and save
     */
    public function saveAsNotified()
    {
        $this->setState(self::STATE_NOTIFIED)->save();
    }

    /**
     * Is flag expired
     *
     * @return bool
     */
    protected function _isExpire()
    {
        return Zend_Date::now()->toValue() - strtotime($this->getLastUpdate()) > self::FLAG_LIFETIME;
    }
}
