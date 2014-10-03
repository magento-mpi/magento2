<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\TaxClass\Type;

use Magento\Customer\Service\V1\Data\CustomerGroupBuilder;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    const GROUP_CODE = 'Test Group';

    /**
     * @magentoDbIsolation enabled
     */
    public function testIsAssignedToObjects()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $builder = $this->_objectManager->create('\Magento\Customer\Service\V1\Data\CustomerGroupBuilder');

        /* Create a tax class */
        $model = $this->_objectManager->create('Magento\Tax\Model\ClassModel');
        $model->setClassName("Test Group Tax Class")
            ->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->isObjectNew(true);
        $model->save();
        $taxClassId = $model->getId();

        $model->setId($taxClassId);
        /** @var $customerGroupService \Magento\Customer\Service\V1\CustomerGroupServiceInterface */
        $customerGroupService = $this->_objectManager->create('\Magento\Customer\Service\V1\CustomerGroupService');
        $group = $builder->setId(null)->setCode(self::GROUP_CODE)->setTaxClassId($taxClassId)
            ->create();
        $customerGroupService->createGroup($group);

        /** @var $model \Magento\Tax\Model\TaxClass\Type\Customer */
        $model = $this->_objectManager->create('Magento\Tax\Model\TaxClass\Type\Customer');
        $model->setId($taxClassId);
        $this->assertTrue($model->isAssignedToObjects());
    }
}

