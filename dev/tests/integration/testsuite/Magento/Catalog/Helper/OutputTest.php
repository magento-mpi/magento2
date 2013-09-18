<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Helper_OutputTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Helper\Output');
    }

    /**
     * addHandler()
     * getHandlers()
     */
    public function testAddHandlerGetHandlers()
    {
        // invalid handler
        $this->_helper->addHandler('method', 'handler');
        $this->assertEquals(array(), $this->_helper->getHandlers('method'));

        // add one handler
        $objectOne = new StdClass;
        $this->_helper->addHandler('valid', $objectOne);
        $this->assertSame(array($objectOne), $this->_helper->getHandlers('valid'));

        // add another one
        $objectTwo = new StdClass;
        $this->_helper->addHandler('valid', $objectTwo);
        $this->assertSame(array($objectOne, $objectTwo), $this->_helper->getHandlers('valid'));
    }

    public function testProcess()
    {
        $this->_helper->addHandler('sampleProcessor', $this);
        $this->assertStringStartsWith(__CLASS__, $this->_helper->process('sampleProcessor', uniqid(), array()));
    }

    public function testProductAttribute()
    {
        $this->_testAttribute(
            'productAttribute', \Magento\Catalog\Model\Product::ENTITY, "&lt;p&gt;line1&lt;/p&gt;<br />\nline2"
        );
    }

    public function testCategoryAttribute()
    {
        $this->_testAttribute(
            'categoryAttribute', \Magento\Catalog\Model\Category::ENTITY, "&lt;p&gt;line1&lt;/p&gt;\nline2"
        );
    }

    /**
     * Helper method for testProcess()
     *
     * @param \Magento\Catalog\Helper\Output $helper
     * @param string $string
     * @param mixed $params
     * @return string
     * @see testProcess()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function sampleProcessor(\Magento\Catalog\Helper\Output $helper, $string, $params)
    {
        return __CLASS__ . $string;
    }

    /**
     * Test productAttribute() or categoryAttribute() method
     *
     * @param string $method
     * @param string $entityCode
     * @param string $expectedResult
     * @throws Exception on assertion failure
     */
    protected function _testAttribute($method, $entityCode, $expectedResult)
    {
        $attributeName = 'description';
        $attribute = Mage::getSingleton('Magento\Eav\Model\Config')->getAttribute($entityCode, $attributeName);
        $isHtml = $attribute->getIsHtmlAllowedOnFront();
        $isWysiwyg = $attribute->getIsWysiwygEnabled();
        $attribute->setIsHtmlAllowedOnFront(0)->setIsWysiwygEnabled(0);

        try {
            $this->assertEquals(
                $expectedResult, $this->_helper->$method(uniqid(), "<p>line1</p>\nline2", $attributeName)
            );

            $attribute->setIsHtmlAllowedOnFront($isHtml)->setIsWysiwygEnabled($isWysiwyg);
        } catch (Exception $e) {
            $attribute->setIsHtmlAllowedOnFront($isHtml)->setIsWysiwygEnabled($isWysiwyg);
            throw $e;
        }
    }
}
