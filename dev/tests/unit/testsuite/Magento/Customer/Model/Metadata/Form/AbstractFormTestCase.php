<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

/** Test Magento\Customer\Model\Metadata\Form\Multiline */
abstract class AbstractFormTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\Stdlib\DateTime\TimezoneInterface */
    protected $localeMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\Locale\ResolverInterface */
    protected $localeResolverMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\Logger */
    protected $loggerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata */
    protected $attributeMetadataMock;

    protected function setUp()
    {
        $this->localeMock = $this->getMockBuilder('Magento\Framework\Stdlib\DateTime\TimezoneInterface')->getMock();
        $this->localeResolverMock = $this->getMockBuilder('Magento\Framework\Locale\ResolverInterface')->getMock();
        $this->loggerMock = $this->getMockBuilder('Magento\Framework\Logger')->disableOriginalConstructor()->getMock();
        $this->attributeMetadataMock = $this->getMockBuilder(
            'Magento\Customer\Service\V1\Data\Eav\AttributeMetadata'
        )->disableOriginalConstructor()->getMock();
    }
}
