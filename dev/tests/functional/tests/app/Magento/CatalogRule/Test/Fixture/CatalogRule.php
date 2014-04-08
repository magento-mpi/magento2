<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Fixture;

use Mtf\System\Config;
use Mtf\Handler\HandlerFactory;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\InjectableFixture;
use Mtf\Repository\RepositoryFactory;

/**
 * Class CatalogRule
 *
 * @package Magento\CatalogRule\Test\Fixture
 */
class CatalogRule extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\CatalogRule\Test\Repository\CatalogPriceRule';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\CatalogRule\Test\Handler\CatalogRule\CatalogRuleInterface';

    /**
     * Constructor
     *
     * @constructor
     * @param Config $configuration
     * @param RepositoryFactory $repositoryFactory
     * @param FixtureFactory $fixtureFactory
     * @param HandlerFactory $handlerFactory
     * @param array $data
     * @param string $dataSet
     * @param bool $persist
     */
    public function __construct(
        Config $configuration,
        RepositoryFactory $repositoryFactory,
        FixtureFactory $fixtureFactory,
        HandlerFactory $handlerFactory,
        array $data = [],
        $dataSet = '',
        $persist = false
    ) {
        parent::__construct(
            $configuration, $repositoryFactory, $fixtureFactory, $handlerFactory, $data, $dataSet, $persist
        );
    }

    protected $name = [
        'attribute_code' => 'name',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'text',
        'group' => 'rule_information',
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'smallint',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'select',
        'group' => 'rule_information',
    ];

    protected $website_ids = [
        'attribute_code' => 'website_ids',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'multiselect',
        'group'=> 'rule_information'
    ];

    protected $customer_group_ids = [
        'attribute_code' => 'customer_group_ids',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'group'=> 'rule_information'
    ];

    protected $simple_action = [
        'attribute_code' => 'simple_action',
        'backend_type' => 'smallint',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'select',
        'group'=> 'actions'
    ];

    protected $discount_amount = [
        'attribute_code' => 'discount_amount',
        'backend_type' => 'decimal',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'text',
        'group'=> 'actions'
    ];

    protected $conditions = [
        'attribute_code' => 'conditions',
        'backend_type' => 'virtual',
        'is_required' => '0',
        'group' => 'conditions',
        'fixture' => 'Magento\CatalogRule\Test\Fixture\Conditions'
    ];

    protected $condition_type = [
        'attribute_code' => 'conditions__1__new_child',
        'backend_type' => 'virtual',
        'is_required' => '0',
        'group' => 'conditions',
        'input' => 'select'
    ];

    protected $condition_value = [
        'attribute_code' => 'conditions__1--1__value',
        'backend_type' => 'virtual',
        'is_required' => '0',
        'group' => 'conditions',
        'fixture' => 'Magento\CatalogRule\Test\Fixture\Conditions'
    ];


    public function getName()
    {
        return $this->getData('name');
    }

    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    public function getWebsiteIds()
    {
        return $this->getData('website_ids');
    }

    public function getCustomerGroupIds()
    {
        return $this->getData('customer_group_ids');
    }

    public function getSimpleAction()
    {
        return $this->getData('simple_action');
    }

    public function getDiscountAmount()
    {
        return $this->getData('discount_amount');
    }

    public function getConditions()
    {
        return $this->getData('conditions');
    }
 }

