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
namespace Magento\Install\Model;

class Observer
{
    /**
     * Install Session
     *
     * @var \Magento\Core\Model\Session\Generic
     */
    protected $_session;

    /**
     * @param \Magento\Core\Model\Session\Generic $session
     */
    public function __construct(\Magento\Core\Model\Session\Generic $session)
    {
        $this->_session = $session;
    }


    /**
     * @param \Magento\Event\Observer $observer
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
