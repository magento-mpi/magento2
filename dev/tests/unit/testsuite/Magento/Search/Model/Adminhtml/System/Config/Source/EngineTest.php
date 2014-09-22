<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Adminhtml\System\Config\Source;

class EngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Solr\Model\Adminhtml\System\Config\Source\Engine
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Solr\Model\Adminhtml\System\Config\Source\Engine();
    }

    /**
     * Check if \Magento\Solr\Model\Adminhtml\System\Config\Source\Engine has method toOptionArray
     */
    public function testToOptionArrayExistence()
    {
        $this->assertTrue(method_exists($this->_model, 'toOptionArray'), 'Required method toOptionArray not exists');
    }

    /**
     * Check output format
     * @depends testToOptionArrayExistence
     */
    public function testToOptionArrayFormat()
    {
        $options = $this->_model->toOptionArray();
        $this->assertNotEmpty($options);
        $labels = array('MySql Fulltext', 'Solr');
        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
            $this->assertContains((string)$option['label'], $labels);
            $this->assertTrue(class_exists($option['value']));
        }
    }
}
