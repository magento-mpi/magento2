<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View;

class DesignLoader
{
    /**
     * Request
     *
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * Application
     *
     * @var \Magento\App\AreaList
     */
    protected $_areaList;

    /**
     * Layout
     *
     * @var \Magento\App\State
     */
    protected $appState;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\AreaList $areaList
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\App\AreaList $areaList,
        \Magento\App\State $appState
    ) {
        $this->_request = $request;
        $this->_areaList = $areaList;
        $this->appState = $appState;
    }

    /**
     * Load design
     *
     * @return void
     */
    public function load()
    {
        $area = $this->_areaList->getArea($this->appState->getAreaCode());
        $area->load(\Magento\App\Area::PART_DESIGN);
        $area->load(\Magento\App\Area::PART_TRANSLATE);
        $area->detectDesign($this->_request);
    }
}
