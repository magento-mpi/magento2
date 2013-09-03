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
 * SOAP adapter.
 *
 * @category   Magento
 * @package    Magento_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Server_Adapter_Soap extends Magento_Object
{
    /**
     * Soap server
     *
     * @var SoapServer
     */
    protected $_soap = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $data = array()
    ) {
        parent::__construct($data);
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Set handler class name for webservice
     *
     * @param string $handler
     * @return Magento_Api_Model_Server_Adapter_Soap
     */
    public function setHandler($handler)
    {
        $this->setData('handler', $handler);
        return $this;
    }

    /**
     * Retrive handler class name for webservice
     *
     * @return string
     */
    public function getHandler()
    {
        return $this->getData('handler');
    }

    /**
     * Set webservice api controller
     *
     * @param Magento_Api_Controller_Action $controller
     * @return Magento_Api_Model_Server_Adapter_Soap
     */
    public function setController(Magento_Api_Controller_Action $controller)
    {
        $this->setData('controller', $controller);
        return $this;
    }

    /**
     * Retrive webservice api controller. If no controller have been set - emulate it by the use of Magento_Object
     *
     * @return Magento_Api_Controller_Action|Magento_Object
     */
    public function getController()
    {
        $controller = $this->getData('controller');

        if (null === $controller) {
            $controller = new Magento_Object(
                array('request' => Mage::app()->getRequest(), 'response' => Mage::app()->getResponse())
            );

            $this->setData('controller', $controller);
        }
        return $controller;
    }

    public function run()
    {
        $apiConfigCharset = $this->_coreStoreConfig->getConfig("api/config/charset");

        if ($this->getController()->getRequest()->getParam('wsdl') !== null) {
            /** @var $wsdlConfig Magento_Api_Model_Wsdl_Config */
            $wsdlConfig = Mage::getModel('Magento_Api_Model_Wsdl_Config');
            $wsdlConfig->setHandler($this->getHandler())
                ->setCacheId('wsdl_config_global_soap')
                ->init();
            $this->getController()->getResponse()
                ->clearHeaders()
                ->setHeader('Content-Type', 'text/xml; charset=' . $apiConfigCharset)
                ->setBody(
                preg_replace(
                    '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                    '<?xml version="$1" encoding="' . $apiConfigCharset . '"?>',
                    $wsdlConfig->getWsdlContent()
                )
            );
        } else {
            try {
                $this->_instantiateServer();

                $content = preg_replace(
                    '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                        '<?xml version="$1" encoding="' . $apiConfigCharset . '"?>',
                    $this->_soap->handle()
                );

                $content = str_ireplace('><', ">\n<", $content);

                $this->getController()->getResponse()
                    ->clearHeaders()
                    ->setHeader('Content-Type', 'text/xml; charset=' . $apiConfigCharset)
                    ->setHeader('Content-Length', strlen($content), true)
                    ->setBody($content);
            } catch (Zend_Soap_Server_Exception $e) {
                $this->fault($e->getCode(), $e->getMessage());
            } catch (Exception $e) {
                $this->fault($e->getCode(), $e->getMessage());
            }
        }

        return $this;
    }

    /**
     * Dispatch webservice fault
     *
     * @param int $code
     * @param string $message
     */
    public function fault($code, $message)
    {
        if ($this->_extensionLoaded()) {
            throw new SoapFault($code, $message);
        } else {
            die('<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                <SOAP-ENV:Body>
                <SOAP-ENV:Fault>
                <faultcode>' . $code . '</faultcode>
                <faultstring>' . $message . '</faultstring>
                </SOAP-ENV:Fault>
                </SOAP-ENV:Body>
                </SOAP-ENV:Envelope>');
        }

    }

    /**
     * Check whether Soap extension is loaded
     *
     * @return boolean
     */
    protected function _extensionLoaded()
    {
        return class_exists('SoapServer', false);
    }

    /**
     * Transform wsdl url if $_SERVER["PHP_AUTH_USER"] is set
     *
     * @param array
     * @return String
     */
    protected function getWsdlUrl($params = null, $withAuth = true)
    {
        $urlModel = Mage::getModel('Magento_Core_Model_Url')
            ->setUseSession(false);

        $wsdlUrl = $params !== null
            ? $urlModel->getUrl('*/*/*', array('_current' => true, '_query' => $params))
            : $urlModel->getUrl('*/*/*');

        if ($withAuth) {
            $phpAuthUser = urlencode($this->getController()->getRequest()->getServer('PHP_AUTH_USER', false));
            $phpAuthPw = urlencode($this->getController()->getRequest()->getServer('PHP_AUTH_PW', false));

            if ($phpAuthUser && $phpAuthPw) {
                $wsdlUrl = sprintf("http://%s:%s@%s", $phpAuthUser, $phpAuthPw, str_replace('http://', '', $wsdlUrl));
            }
        }

        return $wsdlUrl;
    }

    /**
     * Try to instantiate Zend_Soap_Server
     * If schema import error is caught, it will retry in 1 second.
     *
     * @throws Zend_Soap_Server_Exception
     */
    protected function _instantiateServer()
    {
        $apiConfigCharset = $this->_coreStoreConfig->getConfig('api/config/charset');
        $wsdlCacheEnabled = (bool)$this->_coreStoreConfig->getConfig('api/config/wsdl_cache_enabled');

        if ($wsdlCacheEnabled) {
            ini_set('soap.wsdl_cache_enabled', '1');
        } else {
            ini_set('soap.wsdl_cache_enabled', '0');
        }

        $tries = 0;
        do {
            $retry = false;
            try {
                $this->_soap = new \Zend\Soap\Server($this->getWsdlUrl(array("wsdl" => 1)),
                    array('encoding' => $apiConfigCharset));
            } catch (SoapFault $e) {
                if (false !== strpos(
                    $e->getMessage(),
                    "can't import schema from 'http://schemas.xmlsoap.org/soap/encoding/'"
                )
                ) {
                    $retry = true;
                    sleep(1);
                } else {
                    throw $e;
                }
                $tries++;
            }
        } while ($retry && $tries < 5);
        use_soap_error_handler(false);
        $this->_soap
            ->setReturnResponse(true)
            ->setClass($this->getHandler());
    }
}
