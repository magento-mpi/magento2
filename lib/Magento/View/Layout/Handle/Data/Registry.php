<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View\Layout\Handle\Data;

use Magento\View\Context;
use Magento\View\Layout;
use Magento\View\Layout\Element;
use Magento\View\Layout\Handle;
use Magento\View\Layout\Handle\Data;
use Magento\View\Layout\Handle\Render;
use Magento\View\Layout\HandleFactory;
use Magento\Core\Model\BlockFactory;

class Registry
{
    /**
     * @var \Magento\Core\Model\BlockFactory
     */
    protected $blockFactory;

    protected $dataSources = array();

    /**
     * @param BlockFactory $blockFactory
     */
    public function __construct(BlockFactory $blockFactory)
    {
        $this->blockFactory = $blockFactory;
    }

    public function get($name, $class)
    {
        if (!isset($this->dataSources[$name])) {

            if (!class_exists($class)) {
                throw new \Exception(__('Invalid Data Source class name: ' . $class));
            }

            $data = $this->blockFactory->createBlock($class);

            $this->dataSources[$name] = $data;
        }

        return $this->dataSources[$name];
    }
}
