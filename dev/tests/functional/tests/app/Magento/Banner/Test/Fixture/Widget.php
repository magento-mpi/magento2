<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Fixture;

/**
 * Fixture for Banner Rotator.
 */
class Widget extends \Magento\Widget\Test\Fixture\Widget
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Banner\Test\Repository\Widget';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Banner\Test\Handler\Widget\WidgetInterface';
}
