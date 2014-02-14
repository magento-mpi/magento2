<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View;

use Magento\View\Layout;
use Magento\View\Element\BlockInterface;
use Magento\View\Element\BlockFactory;
use Magento\ObjectManager;

/**
 * Class BlockPool
 */
class BlockPool
{
    /**
     * @var \Magento\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var array
     */
    protected $blocks = array();

    /**
     * @param ObjectManager $objectManager
     * @param BlockFactory $blockFactory
     */
    public function __construct(ObjectManager $objectManager, BlockFactory $blockFactory)
    {
        $this->objectManager = $objectManager;
        $this->blockFactory = $blockFactory;
    }

    /**
     * @param string $name
     * @param string $class
     * @param array $arguments [optional]
     * @return BlockPool
     * @throws \InvalidArgumentException
     */
    public function add($name, $class, array $arguments = array())
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(__('Invalid Block class name: ' . $class));
        }

        $block = $this->blockFactory->createBlock($class, $arguments);

        $this->blocks[$name] = $block;

        return $this;
    }

    /**
     * @param string $name
     * @return BlockInterface | null
     */
    public function get($name = null)
    {
        if (!isset($name)) {
            return $this->blocks;
        }

        return isset($this->blocks[$name]) ? $this->blocks[$name] : null;
    }
}
