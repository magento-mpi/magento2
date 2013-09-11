<?php
/**
 * Front controller associated with API area.
 *
 * The main responsibility of this class is to identify requested API type and instantiate correct dispatcher for it.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller;

class Front implements \Magento\Core\Controller\FrontInterface
{
    /**#@+
     * API types
     */
    const API_TYPE_REST = 'rest';
    const API_TYPE_SOAP = 'soap';
    /**#@-*/

    /**
     * Specific front controller for current API type.
     *
     * @var \Magento\Webapi\Controller\DispatcherInterface
     */
    protected $_dispatcher;

    /** @var \Magento\Core\Model\App */
    protected $_application;

    /** @var string */
    protected $_apiType;

    /** @var \Magento\Webapi\Controller\Dispatcher\Factory */
    protected $_dispatcherFactory;

    /** @var \Magento\Controller\Router\Route\Factory */
    protected $_routeFactory;

    /** @var \Magento\Webapi\Controller\Dispatcher\ErrorProcessor */
    protected $_errorProcessor;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Controller\Dispatcher\Factory $dispatcherFactory
     * @param \Magento\Core\Model\App $application
     * @param \Magento\Controller\Router\Route\Factory $routeFactory
     * @param \Magento\Webapi\Controller\Dispatcher\ErrorProcessor $errorProcessor
     */
    public function __construct(
        \Magento\Webapi\Controller\Dispatcher\Factory $dispatcherFactory,
        \Magento\Core\Model\App $application,
        \Magento\Controller\Router\Route\Factory $routeFactory,
        \Magento\Webapi\Controller\Dispatcher\ErrorProcessor $errorProcessor
    ) {
        $this->_dispatcherFactory = $dispatcherFactory;
        $this->_application = $application;
        $this->_routeFactory = $routeFactory;
        $this->_errorProcessor = $errorProcessor;

        ini_set('display_startup_errors', 0);
        ini_set('display_errors', 0);
    }

    /**
     * Dispatch request and send response.
     *
     * @return \Magento\Webapi\Controller\Front
     */
    public function dispatch()
    {
        try {
            $this->_getDispatcher()->dispatch();
        } catch (\Exception $e) {
            $this->_errorProcessor->renderException($e);
        }
        return $this;
    }

    /**
     * Retrieve front controller for concrete API type (factory method).
     *
     * @return \Magento\Webapi\Controller\DispatcherInterface
     * @throws \Magento\Core\Exception
     */
    protected function _getDispatcher()
    {
        if (is_null($this->_dispatcher)) {
            $this->_dispatcher = $this->_dispatcherFactory->get($this->determineApiType());
        }
        return $this->_dispatcher;
    }

    /**
     * Return the list of defined API types.
     *
     * @return array
     */
    public function getListOfAvailableApiTypes()
    {
        return array(
            self::API_TYPE_REST,
            self::API_TYPE_SOAP
        );
    }

    /**
     * Determine current API type using application request (not web API request).
     *
     * @return string
     * @throws \Magento\Core\Exception
     * @throws \Magento\Webapi\Exception If requested API type is invalid.
     */
    public function determineApiType()
    {
        if (is_null($this->_apiType)) {
            $request = $this->_application->getRequest();
            $apiRoutePath = $this->_application->getConfig()->getAreaFrontName()
                . '/:' . \Magento\Webapi\Controller\Request::PARAM_API_TYPE;
            $apiRoute = $this->_routeFactory->createRoute(
                '\Magento\Webapi\Controller\Router\Route',
                $apiRoutePath
            );
            if (!($apiTypeMatch = $apiRoute->match($request, true))) {
                throw new \Magento\Webapi\Exception(__('Request does not match any API type route.'),
                    \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
            }

            $apiType = $apiTypeMatch[\Magento\Webapi\Controller\Request::PARAM_API_TYPE];
            if (!in_array($apiType, $this->getListOfAvailableApiTypes())) {
                throw new \Magento\Webapi\Exception(__('The "%1" API type is not defined.', $apiType),
                    \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
            }
            $this->_apiType = $apiType;
        }
        return $this->_apiType;
    }
}
