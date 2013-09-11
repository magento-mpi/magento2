<?php
/**
 * Factory of web API action controllers (resources).
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Action;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create front controller instance.
     *
     * @param string $className
     * @param \Magento\Webapi\Controller\Request $request
     * @return \Magento\Webapi\Controller\ActionAbstract
     * @throws \InvalidArgumentException
     */
    public function createActionController($className, $request)
    {
        $actionController = $this->_objectManager->create($className, array('request' => $request));
        if (!$actionController instanceof \Magento\Webapi\Controller\ActionAbstract) {
            throw new \InvalidArgumentException(
                'The specified class is not a valid API action controller.');
        }
        return $actionController;
    }
}
