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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Magento_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Run garbage collector for cleaning memory
     *
     * @return void
     */
    public function tearDown()
    {
        gc_collect_cycles();
        parent::tearDown();
    }

    /**
     * Replace object which will be returned on Mage::getSingleton() call
     * @param string $name
     * @param object $mock
     * @return Magento_TestCase
     */
    protected function _replaceSingleton($name, $mock)
    {
        $registryKey = '_singleton/'.$name;
        Mage::unregister($registryKey);
        Mage::register($registryKey, $mock);

        return $this;
    }

    /**
     * Restore original object which will be returned on Mage::getSingleton() call
     * @param string $name
     * @return Magento_TestCase
     */
    protected function _restoreSingleton($name)
    {
        $registryKey = '_singleton/'.$name;
        Mage::unregister($registryKey);

        return $this;
    }

    /**
     * Replace object which will be returned on Mage::getSingleton() call with mock
     * @param string $name
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _replaceSingletonWithMock($name, $methods=array())
    {
        $class = get_class(Mage::getSingleton($name));
        $mock = $this->getMock($class, $methods);

        $this->_replaceSingleton($name, $mock);

        return $mock;
    }

    /**
     * Replace object which will be returned on Mage::helper() call
     * @param string $name
     * @param object $mock
     * @return Magento_TestCase
     */
    protected function _replaceHelper($name, $mock)
    {
        if (strpos($name, '/') === false) {
            $name .= '/data';
        }

        $registryKey = '_helper/' . $name;
        Mage::unregister($registryKey);
        Mage::register($registryKey, $mock);

        return $this;
    }

    /**
     * Restore original object which will be returned on Mage::helper() call
     * @param string $name
     * @return Magento_TestCase
     */
    protected function _restoreHelper($name)
    {
        if (strpos($name, '/') === false) {
            $name .= '/data';
        }

        $registryKey = '_helper/' . $name;
        Mage::unregister($registryKey);

        return $this;
    }

    /**
     * Replace object which will be returned on Mage::helper() call with mock
     * @param string $name
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _replaceHelperWithMock($name, $methods=array())
    {
        $class = get_class(Mage::helper($name));
        $mock = $this->getMock($class, $methods);

        $this->_replaceHelper($name, $mock);

        return $mock;
    }
}
