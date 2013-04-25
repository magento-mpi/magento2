<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Saas_ImportExport_Model_System_Message_Export implements Mage_AdminNotification_Model_System_MessageInterface
{
    /**
     * Message Identity
     */
    const MESSAGE_IDENTITY = 'EXPORT_ENTITY';

    /**
     * Process synchronization flag
     *
     * @var Saas_ImportExport_Model_Flag
     */
    protected $_flag;

    /**
     * Flag is displayed
     *
     * @var bool
     */
    protected $_isDisplayed = null;

    /**
     * Index helper
     *
     * @var Saas_ImportExport_Helper_Data
     */
    protected $_helper;

    /**
     * @param Saas_ImportExport_Model_FlagFactory $flagFactory
     * @param Saas_ImportExport_Helper_Data $helper
     */
    public function __construct(Saas_ImportExport_Model_FlagFactory $flagFactory, Saas_ImportExport_Helper_Data $helper)
    {
        $this->_flag = $flagFactory->create()->loadSelf();
        $this->_helper = $helper;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return self::MESSAGE_IDENTITY;
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        if (null === $this->_isDisplayed) {
            $this->_isDisplayed = $this->_flag->isTaskFinished();
            if ($this->_isDisplayed) {
                $this->_flag->saveAsNotified();
            }
        }
        return $this->_isDisplayed;
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        return $this->_helper->__('The Export task has been finished.');
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
