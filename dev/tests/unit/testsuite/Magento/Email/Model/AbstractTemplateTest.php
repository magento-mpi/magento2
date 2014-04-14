<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Email\Model\AbstractTemplate.
 */
namespace Magento\Email\Model;

class AbstractTemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Template mock
     *
     * @var AbstractTemplate
     */
    protected $_model;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $this->getMockForAbstractClass(
            'Magento\Email\Model\AbstractTemplate',
            $helper->getConstructArguments(
                'Magento\Email\Model\AbstractTemplate',
                array(
                    'design' => $this->getMock('Magento\View\DesignInterface'),
                    'data' => array('area' => \Magento\App\Area::AREA_FRONTEND, 'store' => 1)
                )
            )
        );
    }

    /**
     * @param array $config
     * @expectedException \Magento\Exception
     * @dataProvider invalidInputParametersDataProvider
     */
    public function testSetDesignConfigWithInvalidInputParametersThrowsException($config)
    {
        $this->_model->setDesignConfig($config);
    }

    public function testSetDesignConfigWithValidInputParametersReturnsSuccess()
    {
        $config = array('area' => 'some_area', 'store' => 1);
        $this->_model->setDesignConfig($config);
        $this->assertEquals($config, $this->_model->getDesignConfig()->getData());
    }

    public function invalidInputParametersDataProvider()
    {
        return array(array(array()), array(array('area' => 'some_area')), array(array('store' => 'any_store')));
    }
}
