<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\View;

use Magento\Framework\View\Layout;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class BlockPool
 */
class BlockPool
{
    /**
     * Block factory
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * Blocks
     *
     * @var array
     */
    protected $blocks = array();

    /**
     * Constructor
     *
     * @param BlockFactory $blockFactory
     */
    public function __construct(BlockFactory $blockFactory)
    {
        $this->blockFactory = $blockFactory;
    }

    /**
     * Add a block
     *
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
     * Get blocks
     *
     * @param string $name
     * @return BlockInterface|null
     */
    public function get($name = null)
    {
        if (!isset($name)) {
            return $this->blocks;
        }

        return isset($this->blocks[$name]) ? $this->blocks[$name] : null;
    }
}
