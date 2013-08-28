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
    protected $_handlers = array();

    /**
     * @param Magento_ObjectManager $objectManager
     * @param array $handlerClassesList
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        array $handlerClassesList
    ) {
        foreach ($handlerClassesList as $handlerInfo) {
            if (isset($handlerInfo['instance'])) {
                $this->_handlers[$handlerInfo['sortOrder']] = $objectManager->create($handlerInfo['instance']);
            }
        }

        ksort($this->_handlers);
    }

    /**
     * Get noRoute handlers
     *
     * @return array
     */
    public function getHandlers()
    {
        return $this->_handlers;
    }
}
