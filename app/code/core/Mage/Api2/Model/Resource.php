<?php

/**
 * Base class for all API resources
 *
 * @method Mage_Api2_Model_Request getRequest()
 * @method setRequest(Mage_Api2_Model_Request $request)
 * @method Zend_Controller_Response_Http getResponse()
 * @method setResponse(Zend_Controller_Response_Http $response)
 * @method Mage_Api2_Model_Renderer_Interface getRenderer()
 * @method setRenderer(Mage_Api2_Model_Renderer_Interface $renderer)
 */
abstract class Mage_Api2_Model_Resource extends Varien_Object
{
    const OPERATION_CREATE   = 'create';
    const OPERATION_RETRIEVE = 'retrieve';
    const OPERATION_UPDATE   = 'update';
    const OPERATION_DELETE   = 'delete';

    /**
     * Constructor
     * Set objects for future use in resource models
     *
     * @param Mage_Api2_Model_Request $request
     * @param Zend_Controller_Response_Http $response
     */
    public function __construct(Mage_Api2_Model_Request $request, Zend_Controller_Response_Http $response)
    {
        $this->setRequest($request);
        $this->setResponse($response);

        $this->_initRenderer($request->getAcceptTypes());

        $response->clearHeaders();
        $response->setHeader(
            'Content-Type',
            sprintf('%s; charset=%s', $this->getRenderer()->getMimeType(), Mage_Api2_Model_Response::RESPONSE_CHARSET)
        );
    }

    /**
     * Init renderer
     *
     * @param array $acceptTypes
     * @return Mage_Api2_Model_Resource
     */
    protected function _initRenderer($acceptTypes)
    {
        $renderer = Mage_Api2_Model_Renderer::factory($acceptTypes);
        $this->setRenderer($renderer);
        return $this;
    }

    /**
     * Internal resource model dispatch
     */
    public function dispatch()
    {
        $operation = $this->getRequest()->getOperation();

        switch ($operation) {
            case self::OPERATION_CREATE:
            case self::OPERATION_UPDATE:
                $data = $this->getRequest()->getBodyParams();
                $filtered = $this->getFilter()->in($data);
                $this->$operation($filtered);
                break;

            case self::OPERATION_RETRIEVE:
                $result = $this->retrieve();
                $this->render($result);
                break;

            case self::OPERATION_DELETE:
                $this->delete();
                break;

        }
    }

    /**
     * Render data by renderer and set it into response body
     *
     * @param mixed $data Data to be rendered
     * @return Mage_Api2_Model_Resource
     */
    protected function render($data)
    {
        $this->getResponse()->setBody(
            $this->getRenderer()->render($data, array('encoding' => Mage_Api2_Model_Response::RESPONSE_CHARSET))
        );

        return $this;
    }

    /**
     * Throw exception
     *
     * @param string $message
     * @param int $code
     * @throws Mage_Api2_Exception
     */
    protected function fault($message, $code)
    {
        throw new Mage_Api2_Exception($message, $code);
    }

    /**
     * Get Attributes filter
     *
     * @return Mage_Api2_Model_Acl_Filter
     */
    protected function getFilter()
    {
        /** @var $filter Mage_Api2_Model_Acl_Filter */
        $filter = Mage::getSingleton('api2/acl_filter');

        return $filter;
    }

    /**
     * Dummy method to be replaced in descendants
     */
    protected function retrieve()
    {
        $this->fault('Resource does not support method.', 405);
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @param array $data
     */
    protected function create(array $data)
    {
        $this->fault('Resource does not support method.', 405);
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @param array $data
     */
    protected function update(array $data)
    {
        $this->fault('Resource does not support method.', 405);
    }

    protected function delete()
    {
        $this->fault('Resource does not support method.', 405);
    }
}
