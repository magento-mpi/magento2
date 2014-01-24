<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Model\Resource;


class SetupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftWrapping\Model\Resource\Setup
     */
    protected $giftRegistrySetup;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeConfigMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->typeConfigMock = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface');
        $this->giftRegistrySetup = $helper->getObject('Magento\GiftWrapping\Model\Resource\Setup', array(
                'productTypeConfig' => $this->typeConfigMock
            )
        );
    }

    public function testGetRealProductTypes()
    {
        $types = array(
            'simple' => array('customAttributes' => array('is_real_product' => 'true')),
            'grouped' => array('customAttributes' => array('is_real_product' => 'some_value')),
            'some_product' => array('customAttributes' => array('is_real_product' => 'some_value'),
                'custom_attributes' => array('is_real_product' => 'true'))
        );
        $this->typeConfigMock->expects($this->once())->method('getAll')->will($this->returnValue($types));
        $this->assertEquals(array('simple', 'grouped'), $this->giftRegistrySetup->getRealProductTypes());
    }
}

