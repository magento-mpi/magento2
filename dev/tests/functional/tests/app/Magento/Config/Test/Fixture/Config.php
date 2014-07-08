<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Config\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class Config
 * Config fixture
 */
class Config extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Config\Test\Repository\Config';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Config\Test\Handler\Config\ConfigInterface';

    /**
     * @var array
     */
    protected $section = [
        'attribute_code' => 'section',
        'backend_type' => 'virtual',
    ];
}
