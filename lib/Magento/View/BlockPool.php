<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\View;

use Magento\View\Layout;
use Magento\Core\Model\BlockFactory;
use Magento\ObjectManager;

class BlockPool
{
    /**
     * @var \Magento\Core\Model\BlockFactory
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
     * @return \Magento\Core\Block\AbstractBlock
     * @throws \Exception
     */
    public function add($name, $class, array $arguments = array())
    {
        if (!class_exists($class)) {
            throw new \Exception(__('Invalid Block class name: ' . $class));
        }

        $block = $this->blockFactory->createBlock($class, $arguments);

        $this->blocks[$name] = $block;

        return $block;
    }

    /**
     * @param string $name
     * @return \Magento\Core\Block\AbstractBlock | null
     */
    public function get($name = null)
    {
        if (!isset($name)) {
            return $this->blocks;
        }

        return isset($this->blocks[$name]) ? $this->blocks[$name] : null;
    }
}
