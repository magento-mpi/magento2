<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Order_Pdf_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test protected method to reduce testing complexity, which would be too high in case of testing a public method
     * without completing a huge refactoring of the class.
     */
    function testInsertTotals()
    {
        // Setup parameters, that will be passed to the tested model method
        $page = $this->getMock('Zend_Pdf_Page', array(), array(), '', false);

        $order = new StdClass;
        $source = $this->getMock('Magento_Sales_Model_Order_Invoice', array(), array(), '', false);
        $source->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($order));

        // Setup most constructor dependencies
        $paymentData = $this->getMock('Magento_Payment_Helper_Data', array(), array(), '', false);
        $coreData = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $coreString = $this->getMock('Magento_Core_Helper_String', array(), array(), '', false);
        $coreStoreConfig = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $translate = $this->getMock('Magento_Core_Model_Translate', array(), array(), '', false);
        $dirs = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false);

        // Setup config file totals
        $configTotals = array(
            'item1' => array(''),
            'item2' => array('model' => 'custom_class'),
        );
        $pdfConfig = $this->getMock('Magento_Sales_Model_Order_Pdf_Config', array(), array(), '', false);
        $pdfConfig->expects($this->once())
            ->method('getTotals')
            ->will($this->returnValue($configTotals));

        // Setup total factory
        $total1 = $this->getMock('Magento_Sales_Model_Order_Pdf_Total_Default',
            array('setSource', 'setOrder', 'canDisplay', 'getTotalsForDisplay'), array(), '', false);
        $total1->expects($this->once())
            ->method('setOrder')
            ->with($order)
            ->will($this->returnSelf());
        $total1->expects($this->once())
            ->method('setSource')
            ->with($source)
            ->will($this->returnSelf());
        $total1->expects($this->once())
            ->method('canDisplay')
            ->will($this->returnValue(true));
        $total1->expects($this->once())
            ->method('getTotalsForDisplay')
            ->will($this->returnValue(array(array('label' => 'label1', 'font_size' => 1, 'amount' => '$1'))));

        $total2  = $this->getMock('Magento_Sales_Model_Order_Pdf_Total_Default',
            array('setSource', 'setOrder', 'canDisplay', 'getTotalsForDisplay'), array(), '', false);
        $total2->expects($this->once())
            ->method('setOrder')
            ->with($order)
            ->will($this->returnSelf());
        $total2->expects($this->once())
            ->method('setSource')
            ->with($source)
            ->will($this->returnSelf());
        $total2->expects($this->once())
            ->method('canDisplay')
            ->will($this->returnValue(true));
        $total2->expects($this->once())
            ->method('getTotalsForDisplay')
            ->will($this->returnValue(array(array('label' => 'label2', 'font_size' => 2, 'amount' => '$2'))));

        $valueMap = array(
            array(null, array(), $total1),
            array('custom_class', array(), $total2),
        );
        $totalFactory = $this->getMock('Magento_Sales_Model_Order_Pdf_Total_Factory', array(), array(), '', false);
        $totalFactory->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValueMap($valueMap));

        // Test model
        /** @var Magento_Sales_Model_Order_Pdf_Abstract|PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this->getMockForAbstractClass('Magento_Sales_Model_Order_Pdf_Abstract',
            array($paymentData, $coreData, $coreString, $coreStoreConfig, $translate, $dirs, $pdfConfig, $totalFactory),
            '', true, false, true, array('drawLineBlocks')
        );
        $model->expects($this->once())
            ->method('drawLineBlocks')
            ->will($this->returnValue($page));

        $reflectionMethod = new ReflectionMethod('Magento_Sales_Model_Order_Pdf_Abstract', 'insertTotals');
        $reflectionMethod->setAccessible(true);
        $actual = $reflectionMethod->invoke($model, $page, $source);

        $this->assertSame($page, $actual);
    }
}
