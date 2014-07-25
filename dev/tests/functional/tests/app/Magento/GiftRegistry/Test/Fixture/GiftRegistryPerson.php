<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class GiftRegistryPerson
 *
 * @package Magento\GiftRegistry\Test\Fixture
 */
class GiftRegistryPerson extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\GiftRegistry\Test\Repository\GiftRegistryPerson';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\GiftRegistry\Test\Handler\GiftRegistryPerson\GiftRegistryPersonInterface';

    protected $defaultDataSet = [
    ];

    protected $person_id = [
        'attribute_code' => 'person_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $entity_id = [
        'attribute_code' => 'entity_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $firstname = [
        'attribute_code' => 'firstname',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $lastname = [
        'attribute_code' => 'lastname',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $email = [
        'attribute_code' => 'email',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $role = [
        'attribute_code' => 'role',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $custom_values = [
        'attribute_code' => 'custom_values',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    public function getPersonId()
    {
        return $this->getData('person_id');
    }

    public function getEntityId()
    {
        return $this->getData('entity_id');
    }

    public function getFirstname()
    {
        return $this->getData('firstname');
    }

    public function getLastname()
    {
        return $this->getData('lastname');
    }

    public function getEmail()
    {
        return $this->getData('email');
    }

    public function getRole()
    {
        return $this->getData('role');
    }

    public function getCustomValues()
    {
        return $this->getData('custom_values');
    }
}
