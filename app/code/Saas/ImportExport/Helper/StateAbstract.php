<?php
/**
 * Saas abstract state helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Saas_ImportExport_Helper_StateAbstract extends Mage_Core_Helper_Abstract
{
    /**
     * Process flag
     *
     * @var Saas_ImportExport_Model_StateFlag
     */
    protected $_stateFlag;

    /**
     * Constructor
     *
     * @param Mage_Core_Helper_Context $context
     * @param Saas_ImportExport_Model_StateFlag $stateFlag
     * @param Saas_ImportExport_Helper_Data $dataHelper
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Saas_ImportExport_Model_StateFlag $stateFlag,
        Saas_ImportExport_Helper_Data $dataHelper
    ) {
        $this->_stateFlag = $stateFlag;
        $dataHelper->registerShutdownFunction($this, 'onValidationShutdown');
        parent::__construct($context);
    }

    /**
     * Check if import/export process is in progress
     *
     * @return bool
     */
    public function isInProgress()
    {
        return $this->_stateFlag->isInProgress();
    }

    /**
     * Is task is finished
     *
     * @return bool
     */
    public function isTaskFinished()
    {
        return $this->_stateFlag->isTaskFinished();
    }

    /**
     * Mark export task as queued
     *
     * @return Saas_ImportExport_Helper_Import_State
     */
    public function setTaskAsQueued()
    {
        $this->_stateFlag->saveAsQueued();
        return $this;
    }

    /**
     * Mark export task as processing
     *
     * @return Saas_ImportExport_Helper_Import_State
     */
    public function setTaskAsProcessing()
    {
        $this->_stateFlag->saveAsProcessing();
        return $this;
    }

    /**
     * Mark export task as finished
     *
     * @return Saas_ImportExport_Helper_Import_State
     */
    public function setTaskAsFinished()
    {
        $this->_stateFlag->saveAsFinished();
        return $this;
    }

    /**
     * Mark export task as notified
     *
     * @return Saas_ImportExport_Helper_Import_State
     */
    public function setTaskAsNotified()
    {
        $this->_stateFlag->saveAsNotified();
        return $this;
    }

    /**
     * Shutdown function for processing PHP Fatal Errors during validation import data
     */
    abstract public function onValidationShutdown();
}
