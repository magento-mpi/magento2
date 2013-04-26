<?php
/**
 * Saas Import Helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Import_Validation extends Mage_Core_Helper_Abstract
{
    /**
     * Process flag
     *
     * @var Saas_ImportExport_Model_Import_Validation_Flag
     */
    protected $_progressFlag;

    /**
     * @var Saas_ImportExport_Helper_Shutdown_Handler
     */
    protected $_shutdownHandler;

    /**
     * @param Saas_ImportExport_Model_Import_Validation_FlagFactory $flagFactory
     * @param Saas_ImportExport_Helper_Shutdown_Handler $shutdownHandler
     */
    public function __construct(
        Saas_ImportExport_Model_Import_Validation_FlagFactory $flagFactory,
        Saas_ImportExport_Helper_Shutdown_Handler $shutdownHandler
    ) {
        $this->_progressFlag = $flagFactory->create()->loadSelf();
        $this->_shutdownHandler = $shutdownHandler;
    }

    /**
     * Check if validation is in progress
     *
     * @return bool
     */
    public function isInProgress()
    {
        return $this->_progressFlag->isInProgress();
    }

    /**
     * Set validation in progress flag
     *
     * @param bool $isInProgressFlag
     * @return Saas_ImportExport_Helper_Import_Validation
     */
    public function setInProgress($isInProgressFlag = true)
    {
        $isInProgressFlag ? $this->_progressFlag->saveAsInProgress() : $this->_progressFlag->saveAsNotInProgress();

        return $this;
    }

    /**
     * Register shutdown function for processing PHP Fatal Errors which had occurred during specified process
     *
     * @return Saas_ImportExport_Helper_Import_Validation
     */
    public function registerShutdownFunction()
    {
        $this->_shutdownHandler->registerShutdownFunction($this, 'onValidationShutdown');

        return $this;
    }

    /**
     * Shutdown function for processing PHP Fatal Errors during validation import data
     */
    public function onValidationShutdown()
    {
        $error = error_get_last();
        if ($error && isset($error['type']) && $error['type'] == E_ERROR && $this->isInProgress()) {
            $this->setInProgress(false);
        }
    }
}
