<?php
/**
 * Http application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

use \Magento\Config\Scope,
    \Magento\App\ObjectManager\ConfigLoader;

class Http implements \Magento\AppInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var AreaList
     */
    protected $_areaList;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Config\Scope
     */
    protected $_configScope;

    /**
     * @var ConfigLoader
     */
    protected $_configLoader;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param AreaList $areaList
     * @param RequestInterface $request
     * @param Scope $configScope
     * @param ConfigLoader $configLoader
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        AreaList $areaList,
        RequestInterface $request,
        Scope $configScope,
        ConfigLoader $configLoader
    ) {
        $this->_objectManager = $objectManager;
        $this->_areaList = $areaList;
        $this->_request = $request;
        $this->_configScope = $configScope;
        $this->_configLoader = $configLoader;
    }

    /**
     * Execute application
     */
    public function execute()
    {
        $areaCode = $this->_areaList->getCodeByFrontName($this->_request->getFrontName());
        $this->_configScope->setCurrentScope($areaCode);
        $this->_objectManager->configure($this->_configLoader->load($areaCode));
        $this->_objectManager->get('Magento\App\FrontControllerInterface')->dispatch($this->_request);
        return 0;
    }
}
