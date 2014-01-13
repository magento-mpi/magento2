<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Helper;

use Magento\TestFramework\Helper\Bootstrap;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Helper\View */
    protected $_helper;

    /** @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $_customerMetadataService;

    protected function setUp()
    {
        $this->_customerMetadataService = $this->getMock(
            'Magento\Customer\Service\V1\CustomerMetadataServiceInterface'
        );
        $this->_helper = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Helper\View',
            array('customerMetadataService' => $this->_customerMetadataService)
        );
        parent::setUp();
    }

    /**
     * @param \Magento\Customer\Service\V1\Dto\Customer $customerDto
     * @param string $expectedCustomerName
     * @param bool $isPrefixAllowed
     * @param bool $isMiddleNameAllowed
     * @param bool $isSuffixAllowed
     * @dataProvider getCustomerNameDataProvider
     */
    public function testGetCustomerName(
        $customerDto,
        $expectedCustomerName,
        $isPrefixAllowed = false,
        $isMiddleNameAllowed = false,
        $isSuffixAllowed = false
    ) {

        $visibleAttribute = $this->getMock('Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata', [], [], '', false);
        $visibleAttribute->expects($this->any())->method('getIsVisible')->will($this->returnValue(true));

        $invisibleAttribute = $this->getMock(
            'Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata',
            [],
            [],
            '',
            false
        );
        $invisibleAttribute->expects($this->any())->method('getIsVisible')->will($this->returnValue(false));

        $this->_customerMetadataService->expects($this->any())
            ->method('getAttributeMetadata')
            ->will(
                $this->returnValueMap(
                    [
                        ['customer', 'prefix', $isPrefixAllowed ? $visibleAttribute : $invisibleAttribute],
                        ['customer', 'middlename', $isMiddleNameAllowed ? $visibleAttribute : $invisibleAttribute],
                        ['customer', 'suffix', $isSuffixAllowed ? $visibleAttribute : $invisibleAttribute],
                    ]
                )
            );

        $this->assertEquals(
            $expectedCustomerName,
            $this->_helper->getCustomerName($customerDto),
            'Full customer name is invalid'
        );
    }

    public function getCustomerNameDataProvider()
    {
        /** @var \Magento\Customer\Service\V1\Dto\CustomerBuilder $customerBuilder */
        $customerBuilder = Bootstrap::getObjectManager()->create('Magento\Customer\Service\V1\Dto\CustomerBuilder');
        return [
            'With disabled prefix, middle name, suffix' => [
                $customerBuilder->setPrefix('prefix')
                    ->setFirstname('FirstName')
                    ->setMiddlename('MiddleName')
                    ->setLastname('LastName')
                    ->setSuffix('suffix')
                    ->create(),
                'FirstName LastName'
            ],
            'With prefix, middle name, suffix' => [
                $customerBuilder->setPrefix('prefix')
                    ->setFirstname('FirstName')
                    ->setMiddlename('MiddleName')
                    ->setLastname('LastName')
                    ->setSuffix('suffix')
                    ->create(),
                'prefix FirstName MiddleName LastName suffix',
                true, // $isPrefixAllowed
                true, // $isMiddleNameAllowed
                true //$isSuffixAllowed
            ],
            'Empty prefix, middle name, suffix' => [
                $customerBuilder->setFirstname('FirstName')->setLastname('LastName')->create(),
                'FirstName LastName',
                true, // $isPrefixAllowed
                true, // $isMiddleNameAllowed
                true //$isSuffixAllowed
            ],
            'Empty prefix and suffix, not empty middle name' => [
                $customerBuilder->setFirstname('FirstName')
                    ->setMiddlename('MiddleName')
                    ->setLastname('LastName')
                    ->create(),
                'FirstName MiddleName LastName',
                true, // $isPrefixAllowed
                true, // $isMiddleNameAllowed
                true //$isSuffixAllowed
            ],
        ];
    }
}
