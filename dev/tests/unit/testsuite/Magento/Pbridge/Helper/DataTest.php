<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Pbridge\Helper\Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_order;

    /**
     * @var array()
     */
    protected $_allItems;

    /**
     * setUp
     */
    protected function setUp()
    {
        $context = $this->getMock('Magento\App\Helper\Context', array(), array(), '', false);
        $coreStoreConfig = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $customerSession = $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false);
        $checkoutSession = $this->getMock('Magento\Checkout\Model\Session', array(), array(), '', false);
        $storeManager = $this->getMock('Magento\Core\Model\StoreManagerInterface', array(), array(), '', false);
        $locale = $this->getMock('Magento\Core\Model\LocaleInterface', array(), array(), '', false);
        $layout = $this->getMock('Magento\View\LayoutInterface', array(), array(), '', false);
        $encryptionFactory = $this->getMock('Magento\Pbridge\Model\EncryptionFactory', array(), array(), '', false);
        $appState = $this->getMock('Magento\App\State', array(), array(), '', false);
        $cartFactory = $this->getMock('Magento\Paypal\Model\CartFactory', array('create'), array(), '', false);

        $this->_order = $this->getMock('Magento\Core\Model\AbstractModel', array(), array(), '', false);
        $this->_allItems = array(
            'parent_item' => 'parent',
            'name' => 'name',
            'qty' => '1',
            'price' => '12.2',
            'original_item' => new \Magento\Object()
        );
        $cartFactory
            ->expects($this->once())
            ->method('create')
            ->with(array('salesModel' => $this->_order))
            ->will($this->returnValue(new \Magento\Object(['amounts' => '28', 'all_items' => $this->_allItems])));

        $this->_model = new \Magento\Pbridge\Helper\Data(
            $context, $coreStoreConfig, $customerSession, $checkoutSession, $storeManager, $locale, $layout,
            $encryptionFactory, $appState, $cartFactory
        );
    }

    public function testPrepareCart()
    {
        $this->assertEquals(array($this->_allItems, '28'), $this->_model->prepareCart($this->_order));
    }
}
