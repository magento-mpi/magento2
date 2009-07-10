<?php
/**
 * Abstract Magento PHPUnit TestCase
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_TestCase extends PHPUnit_Framework_TestCase
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
     * Returns a mock object for the specified model
     * Every subsequent Mage::getModel() call will return mocked model as well
     *
     * @param  string  $modelName
     * @param  array   $methods
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  boolean $callOriginalConstructor
     * @param  boolean $callOriginalClone
     * @param  boolean $callAutoload
     * @return object
     */
    public function getModelMock($modelName)
    {
        $args = func_get_args();
        $model = $args[0];
        $args[0] = Mage::getConfig()->getModelClassName($args[0]);
        Mage::$factoryMocks['model'][$model] = call_user_func_array(array($this, 'getMock'), $args);
        return Mage::$factoryMocks['model'][$model];
    }

    /**
     * Retrieve a mock object for the specified helper
     * Every subsequent Mage::helper() call will return mocked model as well
     *
     * @param  string  $modelName
     * @param  array   $methods
     * @param  array   $arguments
     * @param  string  $mockClassName
     * @param  boolean $callOriginalConstructor
     * @param  boolean $callOriginalClone
     * @param  boolean $callAutoload
     * @return object
     */
    public function getHelper($helper)
    {
        $args = func_get_args();
        if (strpos($helper, '/') === false) {
            $helper .= '/data';
        }
        $args[0] = Mage::getConfig()->getHelperClassName($helper);
        Mage::$factoryMocks['helper'][$helper] = call_user_func_array(array($this, 'getMock'), $args);
        return Mage::$factoryMocks['helper'][$helper];
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

    /**
     * Retrieve DB Adapter instance for Test
     *
     * @return Mage_DbAdapter
     */
    protected function _getDbAdapter()
    {
        return Mage::registry('_dbadapter');
    }
}
