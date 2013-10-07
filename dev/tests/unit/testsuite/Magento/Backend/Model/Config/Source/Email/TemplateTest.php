<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Source\Email;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Source\Email\Template
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Core\Model\Email\Template\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_emailConfig;

    protected function setUp()
    {
        $this->_coreRegistry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false, false);
        $this->_emailConfig = $this->getMock('Magento\Core\Model\Email\Template\Config', array(), array(), '', false);
        $this->_templatesFactory = $this->getMock('Magento\Core\Model\Resource\Email\Template\CollectionFactory',
            array(), array(), '', false);
        $this->_model = new \Magento\Backend\Model\Config\Source\Email\Template(
            $this->_coreRegistry, $this->_templatesFactory, $this->_emailConfig
        );
    }

    public function testToOptionArray()
    {
        $collection = $this->getMock(
            'Magento\Core\Model\Resource\Email\Template\Collection', array(), array(), '', false
        );
        $collection
            ->expects($this->once())
            ->method('toOptionArray')
            ->will($this->returnValue(array(
                array('value' => 'template_one', 'label' => 'Template One'),
                array('value' => 'template_two', 'label' => 'Template Two'),
            )))
        ;
        $this->_coreRegistry
            ->expects($this->once())
            ->method('registry')
            ->with('config_system_email_template')
            ->will($this->returnValue($collection))
        ;
        $this->_emailConfig
            ->expects($this->once())
            ->method('getTemplateLabel')
            ->with('template_new')
            ->will($this->returnValue('Template New'))
        ;
        $expectedResult = array(
            array('value' => 'template_new', 'label' => 'Template New (Default)'),
            array('value' => 'template_one', 'label' => 'Template One'),
            array('value' => 'template_two', 'label' => 'Template Two'),
        );
        $this->_model->setPath('template/new');
        $this->assertEquals($expectedResult, $this->_model->toOptionArray());
    }
}
