<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Install\Model\Wizard
 */
namespace Magento\Install\Model;

/**
 * Class WizardTest
 *
 * @package Magento\Install\Block
 */
class WizardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Install\Model\Config
     */
    protected $_configMock;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_urlBuilderMock;

    /**
     * @var \Magento\Install\Model\Wizard
     */
    protected $_model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_requestMock;

    /**
     * Set up before test
     */
    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_configMock = $this->getMock('\Magento\Install\Model\Config', array(), array(), '', false);
        $this->_configMock->expects($this->any())->method('getWizardSteps')->will($this->returnValue(array()));
        $this->_urlBuilderMock = $this->getMock('\Magento\UrlInterface', array(), array(), '', false);
        $this->_requestMock = $this->getMock('\Magento\App\RequestInterface', array(), array(), '', false);
        $this->_model = $this->_objectManager->getObject('Magento\Install\Model\Wizard', array(
            'urlBuilder' => $this->_urlBuilderMock,
            'installConfig' => $this->_configMock
        ));
    }

    /**
     * Test get step with empty request
     */
    public function testGetStepByRequest()
    {
        $this->assertFalse($this->_model->getStepByRequest($this->_requestMock));
    }
}
