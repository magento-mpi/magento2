<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Downloadable_Model_Sales_Order_Pdf_Items_CreditmemoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Downloadable_Model_Sales_Order_Pdf_Items_Creditmemo
     */
    protected $_model;

    /**
     * @var Magento_Sales_Model_Order|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_order;

    /**
     * @var Magento_Sales_Model_Order_Pdf_Abstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pdf;

    protected function setUp()
    {
        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $arguments = array(
            'productFactory' => $this->getMock(
                'Magento_Catalog_Model_ProductFactory', array(), array(), '', false
            ),
            'templateMailerFactory' => $this->getMock(
                'Magento_Core_Model_Email_Template_MailerFactory', array(), array(), '', false
            ),
            'emailInfoFactory' => $this->getMock(
                'Magento_Core_Model_Email_InfoFactory', array(), array(), '', false
            ),
            'orderItemCollFactory' => $this->getMock(
                'Magento_Sales_Model_Resource_Order_Item_CollectionFactory', array(), array(), '', false
            ),
            'serviceOrderFactory' => $this->getMock(
                'Magento_Sales_Model_Service_OrderFactory', array(), array(), '', false
            ),
            'currencyFactory' => $this->getMock(
                'Magento_Directory_Model_CurrencyFactory', array(), array(), '', false
            ),
            'orderHistoryFactory' => $this->getMock(
                'Magento_Sales_Model_Order_Status_HistoryFactory', array(), array(), '', false
            ),
            'orderTaxCollFactory' => $this->getMock(
                'Magento_Tax_Model_Resource_Sales_Order_Tax_CollectionFactory', array(), array(), '', false
            ),
        );
        $orderConstructorArgs = $objectManager->getConstructArguments('Magento_Sales_Model_Order', $arguments);
        $this->_order = $this->getMock('Magento_Sales_Model_Order', array('formatPriceTxt'), $orderConstructorArgs);
        $this->_order
            ->expects($this->any())
            ->method('formatPriceTxt')
            ->will($this->returnCallback(array($this, 'formatPrice')));

        $this->_pdf = $this->getMock(
            'Magento_Sales_Model_Order_Pdf_Abstract', array('drawLineBlocks', 'getPdf'), array(), '', false, false
        );

        $context = $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false, false);
        $locale = $this->getMock('Magento_Core_Model_Locale_Proxy', array(), array(), '', false, false);
        $modelConstructorArgs = $objectManager
            ->getConstructArguments('Magento_Downloadable_Model_Sales_Order_Pdf_Items_Creditmemo', array(
                'helper' => new Magento_Core_Helper_String($context, $locale)
        ));

        $this->_model = $this->getMock(
            'Magento_Downloadable_Model_Sales_Order_Pdf_Items_Creditmemo',
            array('getLinks', 'getLinksTitle'),
            $modelConstructorArgs
        );

        $context = $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false, false);
        $this->_model->setStringHelper(new Magento_Core_Helper_String($context, $locale));
        $this->_model->setOrder($this->_order);
        $this->_model->setPdf($this->_pdf);
        $this->_model->setPage(new Zend_Pdf_Page('a4'));
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_order = null;
        $this->_pdf = null;
    }

    /**
     * Return price formatted as a string including the currency sign
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return sprintf('$%.2F', $price);
    }

    public function testDraw()
    {
        $expectedPageSettings = array('table_header' => true);
        $expectedPdfPage = new Zend_Pdf_Page('a4');
        $expectedPdfData = array(array(
            'lines' => array(
                array(
                    array('text' => array('Downloadable Documentation'), 'feed' => 35),
                    array('text' => array('downloadable-docu', 'mentation'), 'feed' => 255, 'align' => 'right'),
                    array('text' => '$20.00',   'feed' => 330, 'font' => 'bold', 'align' => 'right'),
                    array('text' => '$-5.00',   'feed' => 380, 'font' => 'bold', 'align' => 'right'),
                    array('text' => '1',        'feed' => 445, 'font' => 'bold', 'align' => 'right'),
                    array('text' => '$2.00',    'feed' => 495, 'font' => 'bold', 'align' => 'right'),
                    array('text' => '$17.00',   'feed' => 565, 'font' => 'bold', 'align' => 'right'),
                ),
                array(
                    array('text' => array('Test Custom Option'), 'font' => 'italic', 'feed' => 35),
                ),
                array(
                    array('text' => array('test value'), 'feed' => 40),
                ),
                array(
                    array('text' => array('Download Links'), 'font' => 'italic', 'feed' => 35),
                ),
                array(
                    array('text' => array('Magento User Guide'), 'feed' => 40),
                ),
            ),
            'height' => 20,
        ));

        $this->_model->setItem(new Magento_Object(array(
            'name'              => 'Downloadable Documentation',
            'sku'               => 'downloadable-documentation',
            'row_total'         => 20.00,
            'discount_amount'   => 5.00,
            'qty'               => 1,
            'tax_amount'        => 2.00,
            'hidden_tax_amount' => 0.00,
            'order_item'        => new Magento_Object(array(
                'product_options' => array(
                    'options' => array(
                        array('label' => 'Test Custom Option', 'value' => 'test value'),
                    ),
                ),
            )),
        )));
        $this->_model
            ->expects($this->any())
            ->method('getLinksTitle')
            ->will($this->returnValue('Download Links'))
        ;
        $this->_model
            ->expects($this->any())
            ->method('getLinks')
            ->will($this->returnValue(new Magento_Object(array(
                'purchased_items' => array(
                    new Magento_Object(array('link_title' => 'Magento User Guide')),
                )
            ))))
        ;
        $this->_pdf
            ->expects($this->once())
            ->method('drawLineBlocks')
            ->with($this->anything(), $expectedPdfData, $expectedPageSettings)
            ->will($this->returnValue($expectedPdfPage))
        ;

        $this->assertNotSame($expectedPdfPage, $this->_model->getPage());
        $this->_model->draw();
        $this->assertSame($expectedPdfPage, $this->_model->getPage());
    }
}
