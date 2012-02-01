<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Session_Abstract_Varien
 *
 */
class Mage_Core_Model_Session_Abstract_VarienTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider sessionSaveMethodDataProvider
     */
    public function testSessionSaveMethod($saveMethod, $iniValue)
    {
        $this->markTestIncomplete('Bug MAGE-5487');

        // depending on configuration some values cannot be set as default save session handlers.
        $origSessionHandler = ini_set('session.save_handler', $iniValue);
        if ($iniValue && (ini_get($iniValue) != $iniValue)) {
            $this->markTestSkipped("Can't  set '$iniValue' as session save handler");
        }
        ini_set('session.save_handler', $origSessionHandler);

        Mage::getConfig()->setNode(Mage_Core_Model_Session_Abstract::XML_NODE_SESSION_SAVE, $saveMethod);
        /**
         * @var Mage_Core_Model_Session_Abstract_Varien
         */
        $model = new Mage_Core_Model_Session_Abstract();
        $model->start();
        if ($iniValue) {
            $this->assertEquals(ini_get('session.save_handler'), $iniValue);
        }
    }

    public function sessionSaveMethodDataProvider()
    {
        $testCases = array(
            array('db', 'user'),
            array('memcache', 'memcache'),
            array('memcached', 'memcached'),
            array('eaccelerator', 'eaccelerator'),
            array('', ''),
            array('dummy', ''),
        );

        return $testCases;
    }
}
