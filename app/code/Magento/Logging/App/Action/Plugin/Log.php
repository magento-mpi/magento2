<?php
/**
 * Log plugin. Logs user actions
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Logging\App\Action\Plugin;

use Magento\Logging\Model\Processor;

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
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param callable $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\Framework\App\ActionInterface $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $beforeForwardInfo = $request->getBeforeForwardInfo();

        // Always use current action name bc basing on
        // it we make decision about access granted or denied
        $actionName = $request->getRequestedActionName();

        if (empty($beforeForwardInfo)) {
            $fullActionName = $request->getFullActionName();
        } else {
            $fullActionName = [$request->getRequestedRouteName()];

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
