<?php
/**
 * Abstract class for notified status of export/import process
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
abstract class Saas_ImportExport_Model_System_Message_Abstract
    implements Mage_AdminNotification_Model_System_MessageInterface
{
    /**
     * Process state helper
     *
     * @var Saas_ImportExport_Helper_StateAbstract
     */
    protected $_stateHelper;

    /**
     * Index helper
     *
     * @var Saas_ImportExport_Helper_Data
     */
    protected $_helper;

    /**
     * Flag is displayed
     *
     * @var bool
     */
    protected $_isDisplayed = null;

    /**
     * @param Saas_ImportExport_Helper_StateAbstract $stateHelper
     * @param Saas_ImportExport_Helper_Data $helper
     */
    public function __construct(
        Saas_ImportExport_Helper_StateAbstract $stateHelper,
        Saas_ImportExport_Helper_Data $helper
    ) {
        $this->_stateHelper = $stateHelper;
        $this->_helper = $helper;
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        if (null === $this->_isDisplayed) {
            $this->_isDisplayed = $this->_stateHelper->isTaskFinished();
            if ($this->_isDisplayed) {
                $this->_stateHelper->setTaskAsNotified();
            }
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
        return self::SEVERITY_MAJOR;
    }
}
