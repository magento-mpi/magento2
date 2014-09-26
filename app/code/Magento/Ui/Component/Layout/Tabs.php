<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Layout;

use Magento\Ui\Component\AbstractView;
use Magento\Ui\Component\ContextBehaviorInterface;
use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class Tabs
 */
class Tabs extends AbstractView implements ContextBehaviorInterface
{
    /**
     * Context component
     *
     * @var UiComponentInterface
     */
    protected $context;

    /**
     * Set context component
     *
     * @param UiComponentInterface $component
     * @return mixed
     */
    public function setContext(UiComponentInterface $component)
    {
        $this->context = $component;
    }

    /**
     * Get context component
     *
     * @return UiComponentInterface
     */
    public function getContext()
    {
        return isset($this->context) ? $this->context : $this;
    }
}
