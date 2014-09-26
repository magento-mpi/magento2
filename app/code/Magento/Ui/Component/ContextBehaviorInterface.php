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
interface ContextBehaviorInterface
{
    /**
     * Set context component
     *
     * @param UiComponentInterface $component
     * @return mixed
     */
    public function setContext(UiComponentInterface $component);

    /**
     * Get context component
     *
     * @return UiComponentInterface
     */
    public function getContext();
}
