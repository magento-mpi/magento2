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
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Application
     *
     * @var \Magento\Framework\App\AreaList
     */
    protected $_areaList;

    /**
     * Layout
     *
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\AreaList $areaList
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\AreaList $areaList,
        \Magento\Framework\App\State $appState
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
        $area->load(\Magento\Core\Model\App\Area::PART_DESIGN);
        $area->load(\Magento\Core\Model\App\Area::PART_TRANSLATE);
        $area->detectDesign($this->_request);
    }
}
