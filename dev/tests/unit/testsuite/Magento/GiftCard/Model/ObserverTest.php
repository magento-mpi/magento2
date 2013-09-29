<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftCard\Model\Observer
     */
    protected $_model;

    /**
     * Test that dependency injections passed to the constructor will not be duplicated in _data property
     */
    public function testConstructorValidArguments()
    {
        $context = new \Magento\Core\Model\Context(
            $this->getMock('Magento\Core\Model\Logger', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Cache', array(), array(), '', false)
        );
        $coreRegistry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);
        $this->_model = new \Magento\GiftCard\Model\Observer(
            $this->getMockForAbstractClass('Magento\Core\Model\StoreManagerInterface', array(), '', false),
            $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false),
            $this->getMockForAbstractClass('Magento\Core\Model\LocaleInterface', array(), '', false),
            $this->getMock(
                'Magento\Sales\Model\Resource\Order\Invoice\Item\CollectionFactory', array(), array(), '', false
            ),
            $this->getMock('Magento\Core\Model\Email\TemplateFactory', array(), array(), '', false),
            $this->getMock('Magento\Sales\Model\Order\InvoiceFactory', array(), array(), '', false),
            $this->getMock('Magento\Backend\Model\Session', array(), array(), '', false),
            $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false),
            $this->getMock('Magento\GiftCard\Helper\Data', array(), array(), '', false),
            $context,
            $coreRegistry,
            $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false),
            null,
            null,
            array(
            'email_template_model' => $this->getMock('Magento\Core\Model\Email\Template', array(), array(), '', false),
            'custom_field'         => 'custom_value',
        ));
        $this->assertEquals(array('custom_field' => 'custom_value'), $this->_model->getData());
    }

    /**
     * Test that only valid model instance can be passed to the constructor
     *
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorInvalidArgument()
    {
        $context = new \Magento\Core\Model\Context(
            $this->getMock('Magento\Core\Model\Logger', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\CacheInterface', array(), array(), '', false)
        );
        $this->_model = new \Magento\GiftCard\Model\Observer(
            $this->getMockForAbstractClass('Magento\Core\Model\StoreManagerInterface', array(), '', false),
            $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false),
            $this->getMockForAbstractClass('Magento\Core\Model\LocaleInterface', array(), '', false),
            $this->getMock(
                'Magento\Sales\Model\Resource\Order\Invoice\Item\CollectionFactory', array(), array(), '', false
            ),
            $this->getMock('Magento\Core\Model\Email\TemplateFactory', array(), array(), '', false),
            $this->getMock('Magento\Sales\Model\Order\InvoiceFactory', array(), array(), '', false),
            $this->getMock('Magento\Backend\Model\Session', array(), array(), '', false),
            $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false),
            $this->getMock('Magento\GiftCard\Helper\Data', array(), array(), '', false),
            $context,
            $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false),
            null,
            null,
            array('email_template_model' => new \stdClass())
        );
    }
}
