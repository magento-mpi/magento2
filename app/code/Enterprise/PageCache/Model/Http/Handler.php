<?php
/**
 * FPC http handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Model_Http_Handler implements Magento_HTTP_HandlerInterface
{
    /**
     * List of available request processors
     *
     * @var Enterprise_PageCache_Model_RequestProcessorInterface[]
     */
    protected $_processors = array();

    /**
     * @param Mage_Core_Model_Config_Primary $config
     * @param Enterprise_PageCache_Model_RequestProcessorFactory $factory
     */
    public function __construct(
        Mage_Core_Model_Config_Primary $config,
        Enterprise_PageCache_Model_RequestProcessorFactory $factory
    ) {
        $processors = $config->getNode('global/cache/request_processors');
        if ($processors) {
            $processors = $processors->asArray();
            usort($processors, array($this, '_cmp'));

            foreach($processors as $processorConfig) {
                $this->_processors[] = $factory->create($processorConfig['class']);
            }
        }
    }

    /**
     * Sort request processors
     *
     * @param array $processorA
     * @param array $processorB
     * @return int
     */
    protected function _cmp($processorA, $processorB)
    {
        $sortOrderA = intval($processorA['sortOrder']);
        $sortOrderB = intval($processorB['sortOrder']);
        if ($sortOrderA == $sortOrderB) {
            return 0;
        }
        return ($sortOrderA < $sortOrderB) ? -1 : 1;
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
