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
    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Model\LocaleInterface */
    protected $localeMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Logger */
    protected $loggerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata */
    protected $attributeMetadataMock;

    protected function setUp()
    {
        $this->localeMock = $this->getMockBuilder('Magento\Core\Model\LocaleInterface')->getMock();
        $this->loggerMock = $this->getMockBuilder('Magento\Logger')->disableOriginalConstructor()->getMock();
        $this->attributeMetadataMock = $this->getMockBuilder('Magento\Customer\Service\V1\Data\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
