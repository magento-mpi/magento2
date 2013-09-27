<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installation event observer
 */
class Magento_Install_Model_Observer
{
    /**
     * Install Session
     *
     * @var Magento_Core_Model_Session_Generic
     */
    protected $_session;

    /**
     * @param Magento_Core_Model_Session_Generic $session
     */
    public function __construct(Magento_Core_Model_Session_Generic $session)
    {
        $this->_session = $session;
    }


    /**
     * @param Magento_Event_Observer $observer
     * @return $this
     */
    public function bindLocale($observer)
    {
        $locale = $observer->getEvent()->getLocale();
        if ($locale) {
            $choosedLocale = $this->_session->getLocale();
            if ($choosedLocale) {
                $locale->setLocaleCode($choosedLocale);
            }
        }
        return $this;
    }
}
