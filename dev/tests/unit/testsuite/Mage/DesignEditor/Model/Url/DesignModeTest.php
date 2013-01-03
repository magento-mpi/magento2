<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_DesignEditor_Model_Url_DesignModeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Model_Url_DesignMode
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_testData = array(1,'test');

    public function setUp()
    {
        $this->_model = new Mage_DesignEditor_Model_Url_DesignMode($this->_testData);
    }

    public function testGetRoutePath()
    {
        $this->assertEquals('#', $this->_model->getRoutePath());
    }

    public function testGetRouteUrl()
    {
        $this->assertEquals('#', $this->_model->getRouteUrl());
    }

    public function testGetUrl()
    {
        $this->assertEquals('#', $this->_model->getUrl());
    }
}
