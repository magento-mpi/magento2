<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Webservice api abstract
 *
 * @category   Magento
 * @package    Magento_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model;

class Server
{

    /**
     * Api Name by Adapter
     * @var string
     */
    protected $_api = "";

    /**
     * Web service adapter
     *
     * @var \Magento\Api\Model\Server\Adapter\Soap
     */
    protected $_adapter;

    /**
     * Complex retrieve adapter code by calling auxiliary model method
     *
     * @param string $alias Alias name
     * @return string|null Returns NULL if no alias found
     */
    public function getAdapterCodeByAlias($alias)
    {
        /** @var $config \Magento\Api\Model\Config */
        $config  = \Mage::getSingleton('Magento\Api\Model\Config');
        $aliases = $config->getAdapterAliases();

        if (!isset($aliases[$alias])) {
            return null;
        }
        $object = \Mage::getModel($aliases[$alias][0]);
        $method = $aliases[$alias][1];

        if (!method_exists($object, $method)) {
            \Mage::throwException(__('Can not find webservice adapter.'));
        }
        return $object->$method();
    }

    /**
     * Initialize server components
     *
     * @param \Magento\Api\Controller\Action $controller
     * @param string $adapter Adapter name
     * @param string $handler Handler name
     * @return \Magento\Api\Model\Server
     */
    public function init(\Magento\Api\Controller\Action $controller, $adapter = 'default', $handler = 'default')
    {
        $this->initialize($adapter, $handler);

        $this->_adapter->setController($controller);

        return $this;
    }

    /**
     * Initialize server components. Lightweight implementation of init() method
     *
     * @param string $adapterCode Adapter code
     * @param string $handler OPTIONAL Handler name (if not specified, it will be found from config)
     * @return \Magento\Api\Model\Server
     */
    public function initialize($adapterCode, $handler = null)
    {
        /** @var $helper \Magento\Api\Model\Config */
        $helper   = \Mage::getSingleton('Magento\Api\Model\Config');
        $adapters = $helper->getActiveAdapters();

        if (isset($adapters[$adapterCode])) {
            /** @var $adapterModel \Magento\Api\Model\Server\Adapter\Soap */
            $adapterModel = \Mage::getModel((string) $adapters[$adapterCode]->model);

            if (!($adapterModel instanceof \Magento\Api\Model\Server\Adapter\Soap)) {
                \Mage::throwException(__('Please correct the webservice adapter specified.'));
            }
            $this->_adapter = $adapterModel;
            $this->_api     = $adapterCode;

            // get handler code from config if no handler passed as argument
            if (null === $handler && !empty($adapters[$adapterCode]->handler)) {
                $handler = (string) $adapters[$adapterCode]->handler;
            }
            $handlers = $helper->getHandlers();

            if (!isset($handlers->$handler)) {
                \Mage::throwException(__('Please correct the webservice handler specified.'));
            }
            $handlerClassName = (string) $handlers->$handler->model;

            $this->_adapter->setHandler($handlerClassName);
        } else {
            \Mage::throwException(__('Please correct the webservice adapter specified.'));
        }
        return $this;
    }

    /**
     * Run server
     *
     */
    public function run()
    {
        $this->getAdapter()->run();
    }

    /**
     * Get Api name by Adapter
     * @return string
     */
    public function getApiName()
    {
        return $this->_api;
    }

    /**
     * Retrieve web service adapter
     *
     * @return \Magento\Api\Model\Server\Adapter\Soap
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }


} // Class Magento_Api_Model_Server_Abstract End
