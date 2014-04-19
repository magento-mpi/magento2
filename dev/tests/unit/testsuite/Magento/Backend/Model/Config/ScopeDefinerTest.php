<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config;

class ScopeDefinerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\ScopeDefiner
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Magento\Framework\App\RequestInterface', array(), array(), '', false);
        $this->_model = new \Magento\Backend\Model\Config\ScopeDefiner($this->_requestMock);
    }

    public function testGetScopeReturnsDefaultScopeIfNoScopeDataIsSpecified()
    {
        $this->assertEquals(\Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT, $this->_model->getScope());
    }

    public function testGetScopeReturnsStoreScopeIfStoreIsSpecified()
    {
        $this->_requestMock->expects(
            $this->any()
        )->method(
            'getParam'
        )->will(
            $this->returnValueMap(array(array('website', null, 'someWebsite'), array('store', null, 'someStore')))
        );
        $this->assertEquals(\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_model->getScope());
    }

    public function testGetScopeReturnsWebsiteScopeIfWebsiteIsSpecified()
    {
        $this->_requestMock->expects(
            $this->any()
        )->method(
            'getParam'
        )->will(
            $this->returnValueMap(array(array('website', null, 'someWebsite'), array('store', null, null)))
        );
        $this->assertEquals(\Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE, $this->_model->getScope());
    }
}
