<?php
/**
 * Log plugin. Logs user actions
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\App\Action\Plugin;

use \Magento\App\ResponseInterface;
use \Magento\Logging\Model\Processor;

class Log
{
    /**
     * @var Processor
     */
    protected $_processor;

    /**
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        $this->_processor = $processor;
    }

    /**
     * Mark actions for logging, if required
     *
     * @param \Magento\App\ActionInterface $subject
     * @param callable $proceed
     * @param \Magento\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\App\ActionInterface $subject,
        \Closure $proceed,
        \Magento\App\RequestInterface $request
    ) {
        $beforeForwardInfo = $request->getBeforeForwardInfo();

        // Always use current action name bc basing on
        // it we make decision about access granted or denied
        $actionName = $request->getRequestedActionName();

        if (empty($beforeForwardInfo)) {
            $fullActionName = $request->getFullActionName();
        } else {
            $fullActionName = array($request->getRequestedRouteName());

            if (isset($beforeForwardInfo['controller_name'])) {
                $fullActionName[] = $beforeForwardInfo['controller_name'];
            } else {
                $fullActionName[] = $request->getRequestedControllerName();
            }

            if (isset($beforeForwardInfo['action_name'])) {
                $fullActionName[] = $beforeForwardInfo['action_name'];
            } else {
                $fullActionName[] = $actionName;
            }

            $fullActionName = \implode('_', $fullActionName);
        }

        $this->_processor->initAction($fullActionName, $actionName);
        return $proceed($request);
    }
} 
