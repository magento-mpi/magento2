<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

class FrontControllerPool
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var AreaList
     */
    protected $_areas;

    public function __construct(\Magento\ObjectManager $objectManager, AreaList $areas)
    {
        $this->_objectManager = $objectManager;
        $this->_areas = $areas;
    }

    public function getByRequest(RequestInterface $request)
    {
        $frontControllerClass = 'Magento\App\FrontController';
        $pathParts = explode('/', trim($request->getPathInfo(), '/'));
        if ($pathParts) {
            /** If area front name is used it is expected to be set on the first place in path info */
            $frontName = reset($pathParts);
            $areaCode = $this->_areas->getCodeByFrontName($frontName);
            if ($areaCode) {
                $frontClassName = $this->_areas->getFrontControllerClassName($areaCode);
                if ($frontClassName) {
                    $this->_configScope->setCurrentScope($areaCode);
                    $frontControllerClass = $frontClassName;
                    /** Remove area from path info */
                    array_shift($pathParts);
                    $this->getRequest()->setPathInfo('/' . implode('/', $pathParts));
                }
            }
        }
        return $this->_objectManager->get($frontControllerClass);
    }
}