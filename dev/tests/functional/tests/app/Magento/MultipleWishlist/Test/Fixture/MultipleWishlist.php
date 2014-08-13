<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class MultipleWishlist
 * Fixture for MultipleWishlist
 */
class MultipleWishlist extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\MultipleWishlist\Test\Repository\MultipleWishlist';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\MultipleWishlist\Test\Handler\MultipleWishlist\MultipleWishlistInterface';

    protected $defaultDataSet = [
        'name' => 'New multiple wish list',
        'visibility' => 'public'
    ];

    protected $name = [
        'attribute_code' => 'name',
        'backend_type' => 'text',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $visibility = [
        'attribute_code' => 'visibility',
        'backend_type' => 'text',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    public function getName()
    {
        return $this->getData('name');
    }

    public function getVisibility()
    {
        return $this->getData('visibility');
    }
}
