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
     * Flag is displayed
     *
     * @var bool
     */
    protected $_isDisplayed = null;

    /**
     * @param Saas_ImportExport_Helper_StateAbstract $stateHelper
     */
    public function __construct(
        Saas_ImportExport_Helper_StateAbstract $stateHelper
    ) {
        $this->_stateHelper = $stateHelper;
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
