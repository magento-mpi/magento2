<?php
/**
 * FPC http handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_Http_Handler implements Magento_Http_HandlerInterface
{
    /**
     * List of available processors
     *
     * @var Mage_Core_Model_Cache_ProcessorInterface[]
     */
    protected $_processors = array();

    /**
     * @param Mage_Core_Model_Config_Primary $config
     * @param Mage_Core_Model_Cache_ProcessorFactory $factory
     */
    public function __construct(Mage_Core_Model_Config_Primary $config, Mage_Core_Model_Cache_ProcessorFactory $factory)
    {
        $processors = $config->getNode('global/cache/request_processors');
        if ($processors) {
            foreach($processors->asArray() as $className) {
                $this->_processors[] = $factory->create($className);
            }
        }
    }

    /**
     * Handle http request
     *
     * @param Zend_Controller_Request_Http $request
     * @param Zend_Controller_Response_Http $response
     */
    public function handle(Zend_Controller_Request_Http $request, Zend_Controller_Response_Http $response)
    {
        if (empty($this->_processors)) {
            return;
        }

        $response->headersSentThrowsException = Mage::$headersSentThrowsException;

        $content = false;
        foreach ($this->_processors as $processor) {
            $content = $processor->extractContent($request, $response, $content);
        }

        if ($content) {
            $response->appendBody($content);
            $response->sendResponse();
            $request->setDispatched(true);
            return;
        }

        return;
    }
}
