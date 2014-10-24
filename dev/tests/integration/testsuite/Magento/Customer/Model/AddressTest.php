<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model;


class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Address
     */
    protected $addressModel;

    /**
     * @var \Magento\Customer\Model\Data\AddressBuilder
     */
    protected $addressBuilder;

    protected function setUp()
    {
        $this->addressModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Model\Address'
        );
        $this->addressBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Model\Data\AddressBuilder'
        );
    }

    public function testGetDataModel()
    {
        /** @var \Magento\Customer\Model\Data\Address $addressData */
        $addressData = $this->addressBuilder
            ->setId(1)
            ->setCity('CityX')
            ->setCompany('CompanyY')
            ->setPostcode('77777')
            ->create();
        $updatedAddressData = $this->addressModel->updateData($addressData)->getDataModel();

        $this->assertEquals(1, $updatedAddressData->getId());
        $this->assertEquals('CityX', $updatedAddressData->getCity());
        $this->assertEquals('CompanyY', $updatedAddressData->getCompany());
        $this->assertEquals('77777', $updatedAddressData->getPostcode());
    }
}
