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
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Observer
{
    public function bindLocale($observer)
    {
        if ($locale=$observer->getEvent()->getLocale()) {
            if ($choosedLocale = Mage::getSingleton('Mage_Install_Model_Session')->getLocale()) {
                $locale->setLocaleCode($choosedLocale);
            }
        }
        return $this;
    }

    public function installFailure($observer)
    {
        echo "<h2>There was a problem proceeding with Magento installation.</h2>";
        echo "<p>Please contact developers with error messages on this page.</p>";
        echo Mage::printException($observer->getEvent()->getException());
    }
}
