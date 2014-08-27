<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\PageCache\Model\Observer */
    protected $_model;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\PageCache\Model\Config */
    protected $_configMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\PageCache\Cache */
    protected $_cacheMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\View\Element\AbstractBlock */
    protected $_blockMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\View\Layout */
    protected $_layoutMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Event\Observer */
    protected $_observerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\PageCache\Helper\Data */
    protected $_helperMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Cache\TypeListInterface */
    protected $_typeListMock;

    /** @var \Magento\Framework\Object */
    protected $_transport;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\PageCache\Model\Observer */
    protected $_observerObject;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\PageCache\FormKey */
    protected $_formKey;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Session\Generic */
    protected $_session;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Escaper */
    protected $_escaper;

    /**
     * Set up all mocks and data for test
     */
    public function setUp()
    {
        $this->_configMock = $this->getMock(
            'Magento\PageCache\Model\Config',
            array('getType', 'isEnabled'),
            array(),
            '',
            false
        );
        $this->_cacheMock = $this->getMock('Magento\Framework\App\PageCache\Cache', array('clean'), array(), '', false);
        $this->_helperMock = $this->getMock('Magento\PageCache\Helper\Data', array(), array(), '', false);
        $this->_typeListMock = $this->getMock('Magento\Framework\App\Cache\TypeList', array(), array(), '', false);
        $this->_formKey = $this->getMock('Magento\Framework\App\PageCache\FormKey', array(), array(), '', false);
        $this->_session = $this->getMock('Magento\Framework\Session\Generic', array('setData'), array(), '', false);
        $this->_escaper = $this->getMock('\Magento\Framework\Escaper', array('escapeHtml'), array(), '', false);

        $this->_model = new \Magento\PageCache\Model\Observer(
            $this->_configMock,
            $this->_cacheMock,
            $this->_helperMock,
            $this->_typeListMock,
            $this->_formKey,
            $this->_session,
            $this->_escaper
        );
        $this->_observerMock = $this->getMock(
            'Magento\Framework\Event\Observer',
            array('getEvent'),
            array(),
            '',
            false
        );
        $this->_layoutMock = $this->getMock(
            'Magento\Framework\View\Layout',
            array('isCacheable', 'getBlock', 'getUpdate', 'getHandles'),
            array(),
            '',
            false
        );
        $this->_blockMock = $this->getMockForAbstractClass(
            'Magento\Framework\View\Element\AbstractBlock',
            array(),
            '',
            false,
            true,
            true,
            array('getData', 'isScopePrivate', 'getNameInLayout', 'getUrl')
        );
        $this->_transport = new \Magento\Framework\Object(array('output' => 'test output html'));
        $this->_observerObject = $this->getMock('\Magento\Store\Model\Store', array(), array(), '', false);
    }
}
