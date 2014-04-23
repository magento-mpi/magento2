<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Renderer;

class RegionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $regionCollection
     * @dataProvider renderDataProvider
     */
    public function testRender($regionCollection)
    {
        $countryFactoryMock = $this->getMock(
            'Magento\Directory\Model\CountryFactory',
            array('create'),
            array(),
            '',
            false
        );
        $directoryHelperMock = $this->getMock(
            'Magento\Directory\Helper\Data',
            array('isRegionRequired'),
            array(),
            '',
            false
        );
        $escaperMock = $this->getMock('Magento\Escaper', array(), array(), '', false);
        $elementMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            array('getForm', 'getHtmlAttributes'),
            array(),
            '',
            false
        );
        $countryMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            array('getValue'),
            array(),
            '',
            false
        );
        $regionMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            array(),
            array(),
            '',
            false
        );
        $countryModelMock = $this->getMock(
            'Magento\Directory\Model\Country',
            array('setId', 'getLoadedRegionCollection', 'toOptionArray', '__wakeup'),
            array(),
            '',
            false
        );
        $formMock = $this->getMock('Magento\Framework\Data\Form', array('getElement'), array(), '', false);

        $elementMock->expects($this->any())->method('getForm')->will($this->returnValue($formMock));
        $elementMock->expects(
            $this->any()
        )->method(
            'getHtmlAttributes'
        )->will(
            $this->returnValue(
                array(
                    'title',
                    'class',
                    'style',
                    'onclick',
                    'onchange',
                    'disabled',
                    'readonly',
                    'tabindex',
                    'placeholder'
                )
            )
        );
        $formMock->expects(
            $this->any()
        )->method(
            'getElement'
        )->will(
            $this->returnValueMap(array(array('country_id', $countryMock), array('region_id', $regionMock)))
        );
        $countryMock->expects($this->any())->method('getValue')->will($this->returnValue('GE'));
        $directoryHelperMock->expects(
            $this->any()
        )->method(
            'isRegionRequired'
        )->will(
            $this->returnValueMap(array(array('GE', true)))
        );
        $countryFactoryMock->expects($this->once())->method('create')->will($this->returnValue($countryModelMock));
        $countryModelMock->expects($this->any())->method('setId')->will($this->returnSelf());
        $countryModelMock->expects($this->any())->method('getLoadedRegionCollection')->will($this->returnSelf());
        $countryModelMock->expects($this->any())->method('toOptionArray')->will($this->returnValue($regionCollection));

        $model = new \Magento\Customer\Model\Renderer\Region($countryFactoryMock, $directoryHelperMock, $escaperMock);

        $static = new \ReflectionProperty('Magento\Customer\Model\Renderer\Region', '_regionCollections');
        $static->setAccessible(true);
        $static->setValue(array());

        $html = $model->render($elementMock);

        $this->assertContains('required', $html);
        $this->assertContains('required-entry', $html);
    }

    public function renderDataProvider()
    {
        return array(
            'with no defined regions' => array(array()),
            'with defined regions' => array(
                array(
                    new \Magento\Framework\Object(array('value' => 'Bavaria')),
                    new \Magento\Framework\Object(array('value' => 'Saxony'))
                )
            )
        );
    }
}
