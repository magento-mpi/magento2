<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Fixture;

/**
 * Fixture for Widget.
 */
class Widget extends \Magento\Widget\Test\Fixture\Widget
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\AdvancedCheckout\Test\Repository\Widget';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\AdvancedCheckout\Test\Handler\Widget\WidgetInterface';
}
