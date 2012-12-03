<?php
/**
 * Class that uses Firebug for output profiling results
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_Output_Firebug extends Magento_Profiler_Driver_Standard_OutputAbstract
{
    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response;

    /**
     * @var Zend_Wildfire_Plugin_Interface
     */
    protected $_firePhp;

    /**
     * Start output buffering
     */
    public function __construct()
    {
        ob_start();
    }

    /**
     * Display profiling results and flush output buffer
     *
     * @param Magento_Profiler_Driver_Standard_Stat $stat
     */
    public function display(Magento_Profiler_Driver_Standard_Stat $stat)
    {
        $firebugMessage = new Zend_Wildfire_Plugin_FirePhp_TableMessage($this->_renderCaption());
        $firebugMessage->setHeader(array_keys($this->_getColumns()));

        foreach ($this->_getTimerNames($stat) as $timerName) {
            $row = array();
            foreach ($this->_getColumns() as $key) {
                $row[] = $this->_renderColumnValue($stat->fetch($timerName, $key), $key);
            }
            $firebugMessage->addRow($row);
        }

        Zend_Wildfire_Plugin_FirePhp::send($firebugMessage);

        // setup the wildfire channel
        $firebugChannel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $firebugChannel->setRequest($this->_request ? $this->_request : new Zend_Controller_Request_Http());
        $firebugChannel->setResponse($this->_response ? $this->_response : new Zend_Controller_Response_Http());

        // flush the wildfire headers into the response object
        $firebugChannel->flush();

        // send the response headers
        $firebugChannel->getResponse()->sendHeaders();

        ob_end_flush();
    }

    /**
     * Render timer id column value
     *
     * @param string $timerId
     * @return string
     */
    protected function _renderTimerName($timerId)
    {
        $nestingSep = preg_quote(Magento_Profiler::NESTING_SEPARATOR, '/');
        return preg_replace('/.+?' . $nestingSep . '/', '. ', $timerId);
    }

    /**
     * Request setter
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
    }

    /**
     * Response setter
     *
     * @param Zend_Controller_Response_Abstract $response
     */
    public function setResponse(Zend_Controller_Response_Abstract $response)
    {
        $this->_response = $response;
    }
}
