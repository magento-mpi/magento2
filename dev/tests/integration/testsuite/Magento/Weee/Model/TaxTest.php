<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Model;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Customer\Service\V1\Data\CustomerBuilder;
use Magento\Customer\Service\V1\Data\Customer;

/**
 * @magentoDataFixture Magento/Customer/_files/customer_sample.php
 */
class TaxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Weee\Model\Tax
     */
    protected $_model;

    protected function setUp()
    {
        $helper = $this->getMock('Magento\Weee\Helper\Data', [], [], '', false);
        $helper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));
        $attribute = $this->getMock('Magento\Eav\Model\Entity\Attribute', [], [], '', false);
        $attribute->expects($this->any())->method('getAttributeCodesByFrontendType')->will(
            $this->returnValue(['entity_id'])
        );
        $attributeFactory = $this->getMock('Magento\Eav\Model\Entity\AttributeFactory', [], [], '', false);
        $attributeFactory->expects($this->any())->method('create')->will($this->returnValue($attribute));
        $this->_model = Bootstrap::getObjectManager()->create(
            'Magento\Weee\Model\Tax', ['weeeData' => $helper, 'attributeFactory' => $attributeFactory]
        );
    }

    public function testGetProductWeeeAttributes()
    {
        $customerAccountService = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );
        $customerMetadataService = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerMetadataService'
        );
        $customerBuilder = new CustomerBuilder($customerMetadataService);
        $expected = \Magento\Service\DataObjectConverter::toFlatArray($customerAccountService->getCustomer(1));
        $customerBuilder->populateWithArray($expected);
        $customerDataSet = $customerBuilder->create();
        $fixtureGroupCode = 'custom_group';
        $fixtureTaxClassId = 3;
        /** @var \Magento\Customer\Model\Group $group */
        $group = Bootstrap::getObjectManager()->create('Magento\Customer\Model\Group');
        $fixtureGroupId = $group->load($fixtureGroupCode, 'customer_group_code')->getId();
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote');
        $quote->setCustomerGroupId($fixtureGroupId);
        $quote->setCustomerTaxClassId($fixtureTaxClassId);
        $quote->setCustomerData($customerDataSet);
        $shipping = new \Magento\Object([
            'quote' =>  $quote
        ]);
        $product = Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
        $product->setId(1);
        $product->save();
        $weeeTax = Bootstrap::getObjectManager()->create('Magento\Weee\Model\Tax');
        $weeeTaxData = array(
            'website_id' => '1',
            'entity_id' => '1',
            'country' => 'US',
            'value' => '12.4',
            'state' => '0',
            'attribute_id' => '0',
            'entity_type_id' => '0'
        );
        $weeeTax->setData($weeeTaxData);
        $weeeTax->save();
        $amount = $this->_model->getProductWeeeAttributes($product, $shipping);
        $this->assertEquals('12.4000', $amount[0]->getAmount());
    }
}
