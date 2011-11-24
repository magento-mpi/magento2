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

/**
 * Base test case class
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Magento_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Application cache model
     *
     * This model worked with cache of application
     *
     * @var Mage_Core_Model_Cache
     */
    protected $_appCache;

    /**
     * Run garbage collector for cleaning memory
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        //clear garbage in memory
        if (version_compare(PHP_VERSION, '5.3', '>=')) {
            gc_collect_cycles();
        }

        //ever disable secure area on class down
        self::enableSecureArea(false);

        parent::tearDownAfterClass();
    }

    /**
     * Replace object which will be returned on Mage::getSingleton() call
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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

    /**
     * Enable secure/admin area
     *
     * @param bool $flag
     * @return void
     */
    static public function enableSecureArea($flag = true)
    {
        Mage::unregister('isSecureArea');
        if ($flag) {
            Mage::register('isSecureArea', $flag);
        }
    }

    /**
     * Call safe delete for model
     *
     * @param Mage_Core_Model_Abstract $model
     * @param bool $secure
     * @return Magento_TestCase
     */
    protected function _modelCallDelete($model, $secure = false)
    {
        if ($model instanceof Mage_Core_Model_Abstract && $model->getId()) {
            if ($secure) {
                self::enableSecureArea();
            }
            $model->delete();
            if ($secure) {
                self::enableSecureArea(false);
            }
        }
        return $this;
    }

    /**
     * Get application cache model
     *
     * @return Mage_Core_Model_Cache
     */
    protected function _getAppCache()
    {
        if (null === $this->_appCache) {
            //set application path
            $options = Mage::getConfig()->getOptions();
            $currentCacheDir = $options->getCacheDir();
            $currentEtcDir = $options->getEtcDir();
            $appCacheDir = Magento_Test_Bootstrap::getInstance()->getMagentoDir() . DS
                    . trim(TESTS_APP_CACHE_DIR_RELATIVE_PATH, '\\/');
            $appEtcDir = Magento_Test_Bootstrap::getInstance()->getMagentoDir() . DS
                    . trim(TESTS_APP_ETC_DIR_RELATIVE_PATH, '\\/');
            $options->setCacheDir($appCacheDir);
            $options->setEtcDir($appEtcDir);

            $this->_appCache = new Mage_Core_Model_Cache(array(
                'request_processors' => array(
                    'ee' => 'Enterprise_PageCache_Model_Processor'
                )
            ));

            //revert paths options
            $options->setCacheDir($currentCacheDir);
            $options->setEtcDir($currentEtcDir);
        }
        return $this->_appCache;
    }

    /**
     * Clean config cache of application
     *
     * @return bool
     */
    protected function _cleanAppConfigCache()
    {
        return $this->_getAppCache()->clean(Mage_Core_Model_Config::CACHE_TAG);
    }

    /**
     * Update application config data
     *
     * @param string $path              Config path with the form "section/group/node"
     * @param string|int|null $value    Value of config item
     * @param bool $cleanAppCache       If TRUE application cache will be refreshed
     * @param bool $updateLocalConfig   If TRUE local config object will be updated too
     * @return Magento_TestCase
     * @throws Magento_Test_Exception
     */
    protected function _updateAppConfig($path, $value, $cleanAppCache = true, $updateLocalConfig = false)
    {
        list($section, $group, $node) = explode('/', $path);

        if (!$section || !$group || !$node) {
            throw new Magento_Test_Exception(sprintf(
                'Config path must have view as "section/group/node" but now it "%s"',
                $path));
        }

        /** @var $config Mage_Adminhtml_Model_Config_Data */
        $config = Mage::getModel('adminhtml/config_data');
        $data[$group]['fields'][$node]['value'] = $value;
        $config->setSection($section)
                ->setGroups($data)
                ->save();

        //refresh local cache
        if ($cleanAppCache) {
            if ($updateLocalConfig) {
                Mage::getConfig()->reinit();
                Mage::app()->reinitStores();
            }

            if (!$this->_cleanAppConfigCache()) {
                throw new Magento_Test_Exception('Application configuration cache cannot be cleaned.');
            }
        }

        return $this;
    }
}
