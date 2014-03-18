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
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\AreaList $areaList
     * @param LayoutInterface $layout
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\App\AreaList $areaList,
        \Magento\View\LayoutInterface $layout
    ) {
        $this->_request = $request;
        $this->_areaList = $areaList;
        $this->_layout = $layout;
    }

    /**
     * Load design
     *
     * @return void
     */
    public function load()
    {
        $area = $this->_areaList->getArea($this->_layout->getArea());
        $area->load(\Magento\Core\Model\App\Area::PART_DESIGN);
        $area->load(\Magento\Core\Model\App\Area::PART_TRANSLATE);
        $area->detectDesign($this->_request);
    }
}
