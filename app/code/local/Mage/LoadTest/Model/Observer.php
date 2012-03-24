<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * LoadTest Observer
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_LoadTest_Model_Observer
{
    public function preDispatch(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('Mage_LoadTest_Model_Session');
        /* @var $session Mage_LoadTest_Model_Session */
        $controller = $observer->getEvent()->getControllerAction();

        /*if ($session->isEnabled() && !$session->isLoggedIn() && $session->isAcceptedController(get_class($controller))) {
            die();
        }*/
    }

    public function postDispatch(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('Mage_LoadTest_Model_Session');
        /* @var $session Mage_LoadTest_Model_Session */
        $controller = $observer->getEvent()->getControllerAction();

        if ($session->isEnabled() && $session->isAcceptedController(get_class($controller))) {
            $session->prepareOutputData();
            $session->prepareXmlResponse($session->getResult());
        }
    }

    public function prepareLayoutBefore(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('Mage_LoadTest_Model_Session');
        /* @var $session Mage_LoadTest_Model_Session */
        $block = $observer->getEvent()->getBlock();

        $toProcess = $session->isToProcess();
        if ($toProcess) {
            $block->setUseLayout(true);
            //$block->setBlockPath($session->getBlockPath($block));
            $session->layoutStart($block->getNameInLayout());
        }
    }

    public function prepareLayoutAfter(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('Mage_LoadTest_Model_Session');
        /* @var $session Mage_LoadTest_Model_Session */
        $block = $observer->getEvent()->getBlock();

        $toProcess = $session->isToProcess();
        if ($toProcess) {
            $session->layoutStop($block->getNameInLayout());
        }
    }

    public function toHtmlBefore(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('Mage_LoadTest_Model_Session');
        /* @var $session Mage_LoadTest_Model_Session */
        $block = $observer->getEvent()->getBlock();

        $toProcess = $session->isToProcess();
        if ($toProcess) {
            if (!$block->getBlockPath()) {
                $block->setBlockPath($session->getBlockPath($block));
                $session->layoutStart($block->getBlockPath());
            }
            $session->blockStart($block->getNameInLayout(), $block->setUseLayout());
        }
    }
}
