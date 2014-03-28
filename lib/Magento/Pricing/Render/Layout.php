<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Render;

use Magento\Pricing\Object\SaleableInterface;
use Magento\View\LayoutFactory;
use Magento\View\LayoutInterface;

class Layout
{
    /**
     * Layout Interface
     *
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * Constructor
     *
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(LayoutFactory $layoutFactory)
    {
        $this->layout = $layoutFactory->create();
    }

    /**
     * Add handle(s) to layout
     *
     * @param string|string[] $handle
     * @return void
     */
    public function addHandle($handle)
    {
        $this->layout->getUpdate()->addHandle($handle);
    }

    /**
     * Load layout
     *
     * @return void
     */
    public function loadLayout()
    {
        $this->layout->getUpdate()->load();
        $this->layout->generateXml();
        $this->layout->generateElements();
    }

    /**
     * Obtain block object
     *
     * @param string $name
     * @return \Magento\View\Element\AbstractBlock
     */
    public function getBlock($name)
    {
        return $this->layout->getBlock($name);
    }
}
