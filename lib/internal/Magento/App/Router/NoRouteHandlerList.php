<?php
/**
 * No route handlers retriever
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Router;

class NoRouteHandlerList
{
    /**
     * No route handlers instances
     *
     * @var NoRouteHandlerInterface[]
     */
    protected $_handlers;

    /**
     * @var array
     */
    protected $_handlerList;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param array $handlerClassesList
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        array $handlerClassesList
    ) {
        $this->_handlerList = $handlerClassesList;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get noRoute handlers
     *
     * @return NoRouteHandlerInterface[]
     */
    public function getHandlers()
    {
        if (!$this->_handlers) {

            //sorting handlers list
            $sortedHandlersList = array();
            foreach ($this->_handlerList as $handlerInfo) {
                if (isset($handlerInfo['instance']) && isset($handlerInfo['sortOrder'])) {
                    $sortedHandlersList[$handlerInfo['instance']] = $handlerInfo['sortOrder'];
                }
            }

            asort($sortedHandlersList);

            //creating handlers
            foreach (array_keys($sortedHandlersList) as $handlerInstance) {
                $this->_handlers[] = $this->_objectManager->create($handlerInstance);
            }
        }

        return $this->_handlers;
    }
}
