<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Adminhtml\System\Config\Source;

/**
 * @magentoAppArea adminhtml
 */
class EngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Search\Model\Adminhtml\System\Config\Source\Engine
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model= \Mage::getModel('Magento\Search\Model\Adminhtml\System\Config\Source\Engine');
    }

    public function testToOptionArray()
    {
        $options = $this->_model->toOptionArray();
        $this->assertNotEmpty($options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
            $this->assertTrue(class_exists($option['value']));
        }
    }
}
