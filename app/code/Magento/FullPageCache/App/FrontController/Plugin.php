<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\App\FrontController;

class Plugin
{
    /**
     * List of available request processors
     *
     * @var \Magento\FullPageCache\Model\RequestProcessorInterface[]
     */
    protected $_processors = array();

    /**
     * @var \Magento\App\ResponseFactory
     */
    protected $_responseFactory;

    /**
     * @param \Magento\App\ResponseFactory $responseFactory
     * @param \Magento\FullPageCache\Model\RequestProcessorFactory $factory
     * @param array $requestProcessors
     */
    public function __construct(
        \Magento\App\ResponseFactory $responseFactory,
        \Magento\FullPageCache\Model\RequestProcessorFactory $factory,
        array $requestProcessors = array()
    ) {
        $this->_responseFactory = $responseFactory;
        if (!empty($requestProcessors)) {
            usort($requestProcessors, array($this, '_cmp'));

            foreach($requestProcessors as $processorConfig) {
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
     * Extract cached page or call front controller if page is not cached
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch($arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        if (empty($this->_processors)) {
            return $invocationChain->proceed($arguments);
        }

        $request = $arguments[0];
        $content = false;
        $response = $this->_responseFactory->create();
        foreach ($this->_processors as $processor) {
            $content = $processor->extractContent($request, $response, $content);
        }

        if ($content) {
            $response->appendBody($content);
            return $response;
        }
        return $invocationChain->proceed($arguments);
    }
}
