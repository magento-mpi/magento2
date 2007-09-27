<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory module events observer
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_Model_Observer
{
    /**
     * Action preDispatch event observet method
     *
     * @param Varien_Event_Observer $observer
     */
    public function actionPreDispatch($observer)
    {#return;///MOSHE
        $code = Mage::getSingleton('core/store')->getDefaultCurrencyCode();
        if ($code) {
            $currency = Mage::getModel('directory/currency')->load($code);
            Mage::getSingleton('core/store')->setDefaultCurrency($currency);
        }

        if ($newCode = $observer->getEvent()->getControllerAction()->getRequest()->getParam('CURRENCY')) {
            Mage::getSingleton('core/store')->setCurrentCurrencyCode($newCode);
        }

        $code = Mage::getSingleton('core/store')->getCurrentCurrencyCode();
        if ($code) {
            $currency = Mage::getModel('directory/currency')->load($code);
            Mage::getSingleton('core/store')->setCurrentCurrency($currency);
        }

    }
}
