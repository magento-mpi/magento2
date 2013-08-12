<?php
/**
 * Renderers Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_Factory
{
    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create renderer
     *
     * @param string $className
     * @return Magento_Phrase_RendererInterface
     * @throws InvalidArgumentException
     */
    public function create($className)
    {
        $renderer = $this->objectManager->get($className);

        if (!$renderer instanceof Magento_Phrase_RendererInterface) {
            throw new InvalidArgumentException('Wrong renderer ' . $className);
        }
        return $renderer;
    }
}
