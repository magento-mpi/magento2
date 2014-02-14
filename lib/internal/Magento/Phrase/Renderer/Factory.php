<?php
/**
 * Renderers Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Phrase\Renderer;

class Factory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create renderer
     *
     * @param string $className
     * @return \Magento\Phrase\RendererInterface
     * @throws \InvalidArgumentException
     */
    public function create($className)
    {
        $renderer = $this->_objectManager->get($className);

        if (!$renderer instanceof \Magento\Phrase\RendererInterface) {
            throw new \InvalidArgumentException('Wrong renderer ' . $className);
        }
        return $renderer;
    }
}
