<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class ElementRenderer
 */
class ElementRenderer implements ElementRendererInterface
{
    /**
     * Ui component
     *
     * @var UiComponentInterface
     */
    protected $element;

    /**
     * Data to render
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor
     *
     * @param UiComponentInterface $element
     * @param array $data
     */
    public function __construct(UiComponentInterface $element, array $data)
    {
        $this->element = $element;
        $this->data = $data;
    }

    /**
     * Render element
     *
     * @return string
     */
    public function render()
    {
        return $this->element->render($this->data);
    }
}
