<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Control;

/**
 * Interface ActionPoolInterface
 */
interface ActionPoolInterface
{
    /**
     * Add button
     *
     * @param array $button
     * @return void
     */
    public function addButton(array $button);

    /**
     * Add buttons
     *
     * @param array $buttons
     * @return void
     */
    public function addButtons(array $buttons);
}
