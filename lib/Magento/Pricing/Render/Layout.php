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

use Magento\View\LayoutFactory;
use Magento\View\LayoutInterface;

class Layout
{
    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(LayoutFactory $layoutFactory)
    {
        $this->layout = $layoutFactory->create();
    }

    public function addHandle($handle)
    {
        $this->layout->getUpdate()->addHandle($handle);
    }

    public function loadLayout()
    {
        $this->layout->getUpdate()->load();
        $this->layout->generateXml();
        $this->layout->generateElements();
    }

    /**
     * @param $name
     * @return \Magento\View\Element\AbstractBlock
     */
    public function getBlock($name)
    {
        return $this->layout->getBlock($name);
    }
}
