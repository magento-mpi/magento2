<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class ConfigData
 * Config fixture
 */
class ConfigData extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Core\Test\Repository\ConfigData';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Core\Test\Handler\ConfigData\ConfigDataInterface';

    /**
     * @var array
     */
    protected $section = [
        'attribute_code' => 'section',
        'backend_type' => 'virtual',
    ];

    public function getSection()
    {
        return $this->getData('section');
    }
}
