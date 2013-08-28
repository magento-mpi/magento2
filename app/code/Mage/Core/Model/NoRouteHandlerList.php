<?php
/**
 * No route handlers retriever
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_NoRouteHandlerList
{
    /**
     * No route handlers instances
     *
     * @var array
     */
    protected $_handlers;

    /**
     * @var array
     */
    protected $_handlerList;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param array $handlerClassesList
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        array $handlerClassesList
    ) {
        $this->_handlerList = $handlerClassesList;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get noRoute handlers
     *
     * @return array
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
            foreach ($sortedHandlersList as $handlerInstance => $handlerSortOrder) {
                $this->_handlers[] = $this->_objectManager->create($handlerInstance);
            }
        }

        return $this->_handlers;
    }
}
