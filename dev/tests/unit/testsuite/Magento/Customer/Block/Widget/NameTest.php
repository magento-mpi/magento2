<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Widget;

use Magento\Customer\Service\V1\Dto\Customer;

/**
 * Test class for \Magento\Customer\Block\Widget\Name.
 */
class NameTest extends \PHPUnit_Framework_TestCase
{
    /**#@+
     * Constant values used throughout the various unit tests.
     */
    const PREFIX = 'Mr';
    const MIDDLENAME = 'Middle';
    const SUFFIX = 'Jr';
    const CLASS_NAME = 'customer-name';
    const CONTAINER_CLASS_NAME = 'customer-name-prefix-middlename-suffix';
    const STORE_LABEL = 'Store Label';
    /**#@-*/

    /** @var  \Magento\TestFramework\Helper\ObjectManager */
    protected $_objectManager;

    /** @var  \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata */
    protected $_attributeMetadata;

    /** @var  \Magento\Customer\Helper\Data */
    protected $_customerHelper;

    /** @var  \Magento\Escaper */
    protected $_escaper;

    /** @var  \Magento\Customer\Block\Widget\Name */
    protected $_block;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_escaper = $this->getMock('Magento\Escaper', array(), array(), '', false);

        $context = new \Magento\View\Element\Template\Context(
            $this->getMockForAbstractClass('Magento\App\RequestInterface', array(), '', false),
            $this->getMockForAbstractClass('Magento\View\LayoutInterface', array(), '', false),
            $this->getMockForAbstractClass('Magento\Event\ManagerInterface', array(), '', false),
            $this->getMockForAbstractClass('Magento\UrlInterface', array(), '', false),
            $this->getMockForAbstractClass('Magento\TranslateInterface', array(), '', false),
            $this->getMockForAbstractClass('Magento\App\CacheInterface', array(), '', false),
            $this->getMockForAbstractClass('Magento\View\DesignInterface', array(), '', false),
            $this->getMockForAbstractClass('Magento\Session\SessionManagerInterface', array(), '', false),
            $this->getMockForAbstractClass('Magento\Session\SidResolverInterface', array(), '', false),
            $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false),
            $this->getMock('Magento\App\FrontController', array(), array(), '', false),
            $this->getMock('Magento\View\Url', array(), array(), '', false),
            $this->getMockForAbstractClass('Magento\View\ConfigInterface', array(), '', false),
            $this->getMockForAbstractClass('Magento\App\Cache\StateInterface', array(), '', false),
            $this->getMock('Magento\Logger', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\App', array(), array(), '', false),
            $this->_escaper,
            $this->getMock('Magento\Filter\FilterManager', array(), array(), '', false),
            $this->getMockForAbstractClass('Magento\Core\Model\LocaleInterface', array(), '', false),
            $this->getMock('Magento\Filesystem', array(), array(), '', false),
            $this->getMock('Magento\View\FileSystem', array(), array(), '', false),
            $this->getMock('Magento\View\TemplateEnginePool', array(), array(), '', false),
            $this->getMock('Magento\App\State', array(), array(), '', false),
            $this->getMockForAbstractClass('Magento\Core\Model\StoreManagerInterface', array(), '', false)
        );

        $addressHelper = $this->getMock('Magento\Customer\Helper\Address', array(), array(), '', false);
        $metadataService = $this->getMockForAbstractClass(
            'Magento\Customer\Service\V1\CustomerMetadataServiceInterface', array(), '', false
        );
        $this->_customerHelper = $this->getMock('Magento\Customer\Helper\Data', array(), array(), '', false);
        $this->_attributeMetadata =
            $this->getMock('Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata', array(), array(), '', false);
        $metadataService
            ->expects($this->any())
            ->method('getAttributeMetadata')->will($this->returnValue($this->_attributeMetadata));

        $this->_block = new \Magento\Customer\Block\Widget\Name(
            $context, $addressHelper, $metadataService, $this->_customerHelper
        );
    }

    /**
     * Helper method for testing all show*() methods.
     *
     * @param array $data Customer attribute(s)
     */
    private function _setUpShowAttribute(array $data)
    {
        $customer = $this->_objectManager->getObject(
            'Magento\Customer\Service\V1\Dto\Customer', array('data' => $data)
        );

        $this->_block->setForceUseCustomerAttributes(true);
        $this->_block->setObject($customer);

        $this->_attributeMetadata
            ->expects($this->once())->method('getIsVisible')->will($this->returnValue(true));
    }

    /**
     * Helper method for testing all is*Required() methods.
     */
    private function _setUpIsAttributeRequired()
    {
        $this->_block->setForceUseCustomerAttributes(false);
        $this->_block->setForceUseCustomerRequiredAttributes(true);
        $this->_block->setObject(new \StdClass());

        $this->_attributeMetadata->expects($this->at(0))->method('getIsRequired')->will($this->returnValue(false));
        $this->_attributeMetadata->expects($this->at(1))->method('getIsRequired')->will($this->returnValue(true));
        $this->_attributeMetadata->expects($this->at(2))->method('getIsRequired')->will($this->returnValue(true));
    }

    public function testShowPrefix()
    {
        $this->_setUpShowAttribute(array(Customer::PREFIX => self::PREFIX));
        $this->assertTrue($this->_block->showPrefix());
    }

    public function testIsPrefixRequired()
    {
        $this->_setUpIsAttributeRequired();
        $this->assertTrue($this->_block->isPrefixRequired());
    }

    public function testShowMiddlename()
    {
        $this->_setUpShowAttribute(array(Customer::MIDDLENAME, self::MIDDLENAME));
        $this->assertTrue($this->_block->showMiddlename());
    }

    public function testIsMiddlenameRequired()
    {
        $this->_setUpIsAttributeRequired();
        $this->assertTrue($this->_block->isMiddlenameRequired());
    }

    public function testShowSuffix()
    {
        $this->_setUpShowAttribute(array(Customer::SUFFIX => self::SUFFIX));
        $this->assertTrue($this->_block->showSuffix());
    }

    public function testIsSuffixRequired()
    {
        $this->_setUpIsAttributeRequired();
        $this->assertTrue($this->_block->isSuffixRequired());
    }

    public function testGetPrefixOptions()
    {
        $customer = $this->_objectManager->getObject(
            'Magento\Customer\Service\V1\Dto\Customer', array('data' => array(Customer::PREFIX => self::PREFIX))
        );
        $this->_block->setObject($customer);

        $prefixOptions = array(
            'Mr' => 'Mr',
            'Mrs' => 'Mrs',
            'Ms' => 'Ms',
            'Miss' => 'Miss'
        );

        $this->_customerHelper
            ->expects($this->once())->method('getNamePrefixOptions')->will($this->returnValue($prefixOptions));
        $this->_escaper->expects($this->once())->method('escapeHtml')->will($this->returnValue(self::PREFIX));

        $this->assertSame($prefixOptions, $this->_block->getPrefixOptions());
    }

    public function testGetSuffixOptions()
    {
        $customer = $this->_objectManager->getObject(
            'Magento\Customer\Service\V1\Dto\Customer', array('data' => array(Customer::SUFFIX => self::SUFFIX))
        );
        $this->_block->setObject($customer);

        $suffixOptions = array(
            'Jr' => 'Jr',
            'Sr' => 'Sr'
        );

        $this->_customerHelper
            ->expects($this->once())->method('getNameSuffixOptions')->will($this->returnValue($suffixOptions));
        $this->_escaper->expects($this->once())->method('escapeHtml')->will($this->returnValue(self::SUFFIX));

        $this->assertSame($suffixOptions, $this->_block->getSuffixOptions());
    }

    public function testGetClassName()
    {
        $this->assertEquals(self::CLASS_NAME, $this->_block->getClassName());
    }

    public function testGetContainerClassName()
    {
        $this->_attributeMetadata->expects($this->any())->method('getIsVisible')->will($this->returnValue(true));
        $this->assertEquals(self::CONTAINER_CLASS_NAME, $this->_block->getContainerClassName());
    }

    public function testGetStoreLabel()
    {
        $this->_attributeMetadata
            ->expects($this->once())->method('getStoreLabel')->will($this->returnValue(self::STORE_LABEL));
        $this->assertEquals(self::STORE_LABEL, $this->_block->getStoreLabel('store_label'));
    }
}
