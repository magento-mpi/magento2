<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Saas_Index_Model_System_Message_IndexRefreshed implements Mage_AdminNotification_Model_System_MessageInterface
{
    /**
     * Message Identity
     */
    const MESSAGE_IDENTITY = 'INDEX_REFRESH_FINISHED';

    /**
     * Process synchronization flag
     *
     * @var Saas_Index_Model_Flag
     */
    protected $_flag;

    /**
     * Flag is displayed
     *
     * @var bool
     */
    protected $_isDisplayed = null;

    /**
     * @param Saas_Index_Model_FlagFactory $flagFactory
     */
    public function __construct(Saas_Index_Model_FlagFactory $flagFactory)
    {
        $this->_flag = $flagFactory->create()
            ->loadSelf();
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
        return __('Search index has been refreshed');
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
