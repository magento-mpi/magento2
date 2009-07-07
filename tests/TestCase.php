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
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Mage Basic PHPUnit TestCase
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Constructs a test case with the given name.
     *
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_construct();
    }

    /**
     * Additional TestCase initialize
     *
     * @return Mage_TestCase
     */
    protected function _construct()
    {
        return $this;
    }

    /**
     * Initialize session (emulate session start)
     *
     * @return Mage_TestCase
     */
    protected function _initSession()
    {
        if (!is_array($_SESSION)) {
            session_id(md5(time()));
            $_SESSION = array();
        }

        return $this;
    }

    /**
     * Run Controller Action
     *
     * @param string $path
     * @return Mage_Core_Controller_Response_Http
     */
    protected function _runControllerAction($path)
    {
        $this->_initSession();

        $controller = Mage::app()->getFrontController();
        $routers    = $controller->getRouters();
        $request    = $controller->getRequest();

        $request->setControllerModule(null)
            ->setControllerName(null)
            ->setActionName(null);
        $request->setPathInfo($path)->setDispatched(false);

        $controller->getResponse()->clearAllHeaders();
        $controller->getResponse()->clearBody();

        $i = 0;
        while (!$request->isDispatched() && $i++ < 100) {
            foreach ($routers as $router) {
                if ($router->match($controller->getRequest())) {
                    break;
                }
            }
        }

        if ($i > 100) {
            throw new Exception('Front controller reached 100 router match iterations');
        }

        return $controller->getResponse();
    }

    /**
     * Returns a mock object for the specified class.
     *
     * @param  string  $className
     * @param  array   $methods
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  boolean $callOriginalConstructor
     * @param  boolean $callOriginalClone
     * @param  boolean $callAutoload
     * @return object
     * @since  Method available since Release 3.0.0
     */
    public function getPublicMock($className, $methods = array(),
        array $arguments = array(), $mockClassName = '',
        $callOriginalConstructor = true, $callOriginalClone = true,
        $callAutoload = true)
    {
        return $this->getMock($className, $methods, $arguments, $mockClassName,
            $callOriginalConstructor, $callOriginalClone, $callAutoload);
    }

    /**
     * Retrieve Model as Mock object
     *
     * @param string $model
     * @param unknown_type $methods
     * @param unknown_type $arguments
     * @param unknown_type $mockClassName
     * @param unknown_type $callOriginalConstructor
     * @param unknown_type $callOriginalClone
     * @param unknown_type $callAutoload
     * @return Mage_Core_Model_Abstract
     */
    protected function _getMockModel($model, $methods = array(),
        $arguments = array(), $mockClassName = '',
        $callOriginalConstructor = true, $callOriginalClone = true,
        $callAutoload = true)
    {
        Mage::$factoryMocks['model'][$model] = array(
            $this,
            $methods,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload
        );

        $object = Mage::getModel($model);

        unset(Mage::$factoryMocks['model'][$model]);

        return $object;
    }

    /**
     * Obtain model object instance for tests
     *
     * @param string $model name
     * @return object
     */
    protected function _getModelInstance($model = '')
    {
        if (!empty($model) && $model) {
            /* check for existing model */
            return Mage::getModel($model);
        }
        else {
            /* do something here */
            return new stdClass();
        }
    }

    /**
     * Retrieve Header value By Name
     *
     * @param array $headers
     * @param string $name
     * @param string $default
     * @return string
     */
    protected function _getHeaderByName(array $headers, $name, $default = null)
    {
        foreach ($headers as $header) {
            if ($header['name'] == $name) {
                $default = $header['value'];
                break;
            }
        }
        return $default;
    }
}
