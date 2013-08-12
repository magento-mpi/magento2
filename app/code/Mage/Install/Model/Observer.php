<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installation event observer
 */
class Mage_Install_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function bindLocale($observer)
    {
        $locale = $observer->getEvent()->getLocale();
        if ($locale) {
            $choosedLocale = Mage::getSingleton('Mage_Install_Model_Session')->getLocale();
            if ($choosedLocale) {
                $locale->setLocaleCode($choosedLocale);
            }
        }
        return $this;
    }
}
