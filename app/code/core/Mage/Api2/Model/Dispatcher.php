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
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice api2 dispatcher model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Dispatcher
{
    const RESOURCE_CLASS_TEMPLATE = ':resource_:api_:user_V:version';

    /**
     * Load class file, instantiate resource class, set parameters to the instance, run resource internal dispatch
     * method
     *
     * @param Mage_Api2_Model_Request $request
     * @param Mage_Api2_Model_Response $response
     * @return Mage_Api2_Model_Dispatcher
     */
    public function dispatch(Mage_Api2_Model_Request $request, Mage_Api2_Model_Response $response)
    {
        $class = $this->buildClassName($request);

        //comment this to leave the job for autoloader
        $this->_anticipateAutoLoader($class);

        /** @var $model Mage_Api2_Model_Resource */
        $model = new $class($request, $response);   //Fatal error: Class '%s' not found in %s on line %d
                                                    //which can not be caught if autoloader used
        $model->dispatch();

        return $this;
    }

    /**
     * Build resource class name using request params
     *
     * @param Mage_Api2_Model_Request $request
     * @return string
     */
    protected function buildClassName(Mage_Api2_Model_Request $request)
    {
        $accessKey = $request->getAccessKey();
        $user = new Mage_OAuth_Model_User($accessKey);
        $userType = $user->getType('guest');

        $resource = $request->getParam('model');     //set in Mage_Api2_Model_Router::_setRequestParams
        $apiType = ucfirst($request->getApiType());
        $userType = ucfirst($userType);
        $version = $request->getVersion();

        $replace = array(
            ':resource' => $resource,
            ':api'      => $apiType,
            ':user'     => $userType,
            ':version'  => $version,
        );
        $class = strtr(Mage_Api2_Model_Dispatcher::RESOURCE_CLASS_TEMPLATE, $replace);

        return $class;
    }

    /**
     * Replace autoload process to catch possible fatal error.
     * Also requires Mage::setIsDeveloperMode(true) to catch it.
     *
     * @param string $class
     * @throws Mage_Api2_Exception
     * @return Mage_Api2_Model_Dispatcher
     */
    protected function _anticipateAutoLoader($class)
    {
        $classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $class))).'.php';
        try {
            include $classFile;
        } catch (Exception $e) {
            throw new Mage_Api2_Exception(sprintf('File "%s" could not be loaded', $classFile), 500);
        }

        return $this;
    }
}
