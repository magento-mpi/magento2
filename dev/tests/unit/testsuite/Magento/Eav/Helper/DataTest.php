<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Eav\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * Initialize helper
     */
    protected function setUp()
    {
        $context = $this->getMock('\Magento\App\Helper\Context', [], [], '', false);
        $attributeConfig = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Config', [], [], '', false);
        $coreStoreConfig = $this->getMock('\Magento\Core\Model\Store\Config', [], [], '', false);
        $eavConfig = $this->getMock('\Magento\Eav\Model\Config', [], [], '', false);
        $this->_helper = new Data($context, $attributeConfig, $coreStoreConfig, $eavConfig);
        $this->_eavConfig = $eavConfig;
    }

    public function testGetAttributeMetadata()
    {
        $attribute = new \Magento\Object([
            'entity_type_id' => '1',
            'attribute_id'   => '2',
            'backend'        => new \Magento\Object(['table' => 'customer_entity_varchar']),
            'backend_type'   => 'varchar'
        ]);
        $this->_eavConfig->expects($this->once())
            ->method('getAttribute')
            ->will($this->returnValue($attribute));

        $result = $this->_helper->getAttributeMetadata('customer', 'lastname');
        $expected = [
            'entity_type_id' => '1',
            'attribute_id' => '2',
            'attribute_table' => 'customer_entity_varchar',
            'backend_type' => 'varchar'
        ];


        foreach ($result as $key => $value) {
            if (!isset($expected[$key])) {
                $this->fail('Attribute metadata with key "' . $key . '" not found.');
            }

            if ($expected[$key] != $value) {
                $this->fail('Attribute metadata with key "' . $key . '" has invalid value.');
            }
        }
    }
}
