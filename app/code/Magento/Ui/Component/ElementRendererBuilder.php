<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

use Magento\Framework\ObjectManager;
use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class ElementRendererBuilder
 */
class ElementRendererBuilder
{
    /**
     * Object manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Instance class name
     *
     * @var string
     */
    protected $instanceClass = 'Magento\Ui\Component\ElementRenderer';

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create element to the render
     *
     * @param UiComponentInterface $element
     * @param array $renderData
     * @return ElementRendererInterface
     */
    public function create(UiComponentInterface $element, array $renderData)
    {
        return $this->objectManager->create($this->instanceClass, ['element' => $element, 'data' => $renderData]);
    }
}
