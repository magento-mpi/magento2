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
namespace Magento\Core\Model\Session\AbstractSession;

/**
 * Test class for \Magento\Session\SessionManagerInterface
 */
class VarienTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $saveMethod
     * @param string $iniValue
     * @dataProvider sessionSaveMethodDataProvider
     */
    public function testSessionSaveMethod($saveMethod, $iniValue)
    {
        $this->markTestIncomplete('Bug MAGE-5487');
        // depending on configuration some values cannot be set as default save session handlers.
        // in such cases warnings will be generated by php and test will fail
        $origErrorRep = error_reporting(E_ALL ^ E_WARNING);
        $origSessionHandler = ini_set('session.save_handler', $iniValue);
        if ($iniValue && (ini_get('session.save_handler') != $iniValue)) {
            ini_set('session.save_handler', $origSessionHandler);
            error_reporting($origErrorRep);
            $this->markTestSkipped("Can't  set '$iniValue' as session save handler");
        }
        ini_set('session.save_handler', $origSessionHandler);
        /** @var $configModel \Magento\App\ConfigInterface */
        $configModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\ConfigInterface');
        $configModel->setNode(\Magento\Core\Model\Session\Config::PARAM_SESSION_SAVE_METHOD, $saveMethod);
        /**
         * @var \Magento\Session\SessionManagerInterface $model
         */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Session\SessionManagerInterface');
        //There is no any possibility to determine whether session already started or not in php before 5.4
        $model->setSkipEmptySessionCheck(true);
        $model->start();
        if ($iniValue) {
            $this->assertEquals(ini_get('session.save_handler'), $iniValue);
        }
        ini_set('session.save_handler', $origSessionHandler);
        error_reporting($origErrorRep);
    }

    /**
     * @return array
     */
    public function sessionSaveMethodDataProvider()
    {
        return array(
            array('db', 'user'),
            array('memcache', 'memcache'),
            array('memcached', 'memcached'),
            array('eaccelerator', 'eaccelerator'),
            array('', ''),
            array('dummy', ''),
        );
    }
}
