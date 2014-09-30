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
 * Interface ContextBehaviorInterface
 */
interface ContextBehaviorInterface extends UiComponentInterface
{
    /**
     * Set context component
     *
     * @param ContextBehaviorInterface $component
     * @return mixed
     */
    public function setContext(ContextBehaviorInterface $component);

    /**
     * Get context component
     *
     * @return ContextBehaviorInterface
     */
    public function getContext();

    /**
     * Is the object context
     *
     * @return bool
     */
    public function isContext();
}
