<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_Sales_Model_Observer_Backend_CatalogPriceRuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Sales_Model_Observer_Backend_CatalogPriceRule
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_quoteMock;

    public function setUp()
    {
        $this->_quoteMock = $this->getMock('Mage_Sales_Model_Resource_Quote', array(), array(), '', false);
        $this->_model = new Mage_Sales_Model_Observer_Backend_CatalogPriceRule(
            $this->_quoteMock
        );
    }

    public function testDispatch()
    {
        $this->_quoteMock->expects($this->once())->method('markQuotesRecollectOnCatalogRules');
        $this->_model->dispatch();
    }
}
