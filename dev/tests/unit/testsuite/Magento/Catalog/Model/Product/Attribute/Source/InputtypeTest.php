<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Catalog\Model\Product\Attribute\Source;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class InputtypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Catalog\Model\Product\Attribute\Source\Inputtype */
    protected $inputtypeModel;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registry;

    protected function setUp()
    {
        $this->registry = $this->getMock('Magento\Framework\Registry');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->inputtypeModel = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Model\Product\Attribute\Source\Inputtype',
            [
                'coreRegistry' => $this->registry
            ]
        );
    }

    public function testToOptionArray()
    {
        $inputTypesSet = array(
            array('value' => 'text', 'label' => 'Text Field'),
            array('value' => 'textarea', 'label' => 'Text Area'),
            array('value' => 'date', 'label' => 'Date'),
            array('value' => 'boolean', 'label' => 'Yes/No'),
            array('value' => 'multiselect', 'label' => 'Multiple Select'),
            array('value' => 'select', 'label' => 'Dropdown'),
            array('value' => 'price', 'label' => 'Price'),
            array('value' => 'media_image', 'label' => 'Media Image')
        );

        $this->registry->expects($this->once())->method('registry');
        $this->registry->expects($this->once())->method('register');
        $this->assertEquals($inputTypesSet, $this->inputtypeModel->toOptionArray());
    }
}
