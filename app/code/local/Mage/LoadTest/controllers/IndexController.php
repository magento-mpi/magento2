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
 * LoadTest front controller
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
 */



class Mage_LoadTest_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_forward('spider');
    }

    public function spiderAction()
    {
        $session = Mage::getSingleton('Mage_LoadTest_Model_Session');
        /* @var $session Mage_LoadTest_Model_Session */

        $key = $this->getRequest()->getParam('key');
        $session->login($key);
        $session->spiderXml();
        $session->prepareXmlResponse($session->getResult());
    }
}