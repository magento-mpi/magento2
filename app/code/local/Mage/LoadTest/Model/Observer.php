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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    public function toHtmlAfter(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('Mage_LoadTest_Model_Session');
        /* @var $session Mage_LoadTest_Model_Session */
        $block = $observer->getEvent()->getBlock();

        $toProcess = $session->isToProcess();
        if ($toProcess) {
            $session->blockStop($block->getNameInLayout());
        }
    }
}