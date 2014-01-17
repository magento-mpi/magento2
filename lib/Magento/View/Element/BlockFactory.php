<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element;

use Magento\ObjectManager;

/**
 * Class BlockFactory
 */
class BlockFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $blockName
     * @param array $arguments
     * @return mixed
     * @throws \LogicException
     */
    public function createBlock($blockName, array $arguments = array())
    {
        $block = $this->objectManager->create($blockName, $arguments);
        if (!$block instanceof BlockInterface) {
            throw new \LogicException($blockName . ' does not implemented BlockInterface');
        }
        if ($block instanceof Template) {
            $block->setTemplateContext($block);
        }
        return $block;
    }
}
