<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PrintedTemplate_Model_Tax_Order_ItemTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Saas_PrintedTemplate_Model_Tax_Order_Item::getIsTaxAfterDiscount
     */
    public function testGetIsTaxAfterDiscount()
    {
        $item = $this->getMockBuilder('Saas_PrintedTemplate_Model_Tax_Order_Item')
            ->disableOriginalConstructor()
            ->setMethods(array('_construct'))
            ->getMock();

        $item->setIsTaxAfterDiscount(1);
        $this->assertEquals(true, $item->getIsTaxAfterDiscount());

        $item->setIsTaxAfterDiscount(true);
        $this->assertEquals(true, $item->getIsTaxAfterDiscount());

        $item->setIsTaxAfterDiscount(123123);
        $this->assertEquals(true, $item->getIsTaxAfterDiscount());

        $item->setIsTaxAfterDiscount(false);
        $this->assertEquals(false, $item->getIsTaxAfterDiscount());

        $item->setIsTaxAfterDiscount(array());
        $this->assertEquals(false, $item->getIsTaxAfterDiscount());

        $item->setIsTaxAfterDiscount(0);
        $this->assertEquals(false, $item->getIsTaxAfterDiscount());
    }

    public function testGetIsDiscountOnInclTax()
    {
        $item = $this->getMockBuilder('Saas_PrintedTemplate_Model_Tax_Order_Item')
            ->disableOriginalConstructor()
            ->setMethods(array('_construct'))
            ->getMock();

        $this->assertEquals(false, $item->getIsDiscountOnInclTax());

        $item->setIsDiscountOnInclTax(1);
        $this->assertEquals(true, $item->getIsDiscountOnInclTax());

        $item->setIsDiscountOnInclTax(0);
        $this->assertEquals(false, $item->getIsDiscountOnInclTax());
    }
}
