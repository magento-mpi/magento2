<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installation event observer
 */
namespace Magento\Install\Model;

class Observer
{
    /**
     * Install Session
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $_session;

    /**
     * @param \Magento\Framework\Session\Generic $session
     */
    public function __construct(\Magento\Framework\Session\Generic $session)
    {
        $this->_session = $session;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
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
