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
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * Layout
     *
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Constructor
     *
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\App $app
     * @param \Magento\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Core\Model\App $app,
        \Magento\View\LayoutInterface $layout
    ) {
        $this->_request = $request;
        $this->_app = $app;
        $this->_layout = $layout;
    }

    /**
     * Load design
     *
     * @return void
     */
    public function load()
    {
        $area = $this->_app->getArea($this->_layout->getArea());
        $area->load(\Magento\Core\Model\App\Area::PART_DESIGN);
        $area->load(\Magento\Core\Model\App\Area::PART_TRANSLATE);
        $area->detectDesign($this->_request);
    }
}
