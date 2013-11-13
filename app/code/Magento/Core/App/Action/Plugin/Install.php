<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\App\Action\Plugin;


class Install
{
    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\App\ResponseFactory
     */
    protected $_responseFactory;

    /**
     * @var \Magento\Core\Model\Url
     */
    protected $_url;

    /**
     * @param \Magento\App\State $appState
     * @param \Magento\App\ResponseFactory $responseFactory
     * @param \Magento\Core\Model\Url $url
     */
    public function __construct(
        \Magento\App\State $appState,
        \Magento\App\ResponseFactory $responseFactory,
        \Magento\Core\Model\Url $url
    ) {
        $this->_appState = $appState;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
    }

    /**
     * Dispatch request
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\App\ResponseInterface|mixed
     */
    public function aroundDispatch(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        if (!$this->_appState->isInstalled()) {
            $response = $this->_responseFactory->create();
            $response->setRedirect(
                $this->_url->getUrl('install')
            );
            return $response;
        }
        return $invocationChain->proceed($arguments);
    }
} 