<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Helper;

class EavTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Helper\Eav
     */
    protected $_model;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $collectionFactory = $this->getMock('Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory',
            array('create'), array(), '', false);
        $attributeConfig = $this->getMock('Magento\Eav\Model\Entity\Attribute\Config',
            array(), array(), '', false);
        $this->_model = $helper->getObject('Magento\Rma\Helper\Eav', array(
            'collectionFactory' => $collectionFactory,
            'attributeConfig' => $attributeConfig,
            'context' => $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false)
        ));
    }

    /**
     * @param $validateRules
     * @param array $additionalClasses
     * @internal param array $attributeValidateRules
     * @dataProvider getAdditionalTextElementClassesDataProvider
     */
    public function testGetAdditionalTextElementClasses($validateRules, $additionalClasses)
    {
        $attributeMock = new \Magento\Object(
            array('validate_rules' => $validateRules)
        );
        $this->assertEquals($this->_model->getAdditionalTextElementClasses($attributeMock), $additionalClasses);
    }

    /**
     * @return array
     */
    public function getAdditionalTextElementClassesDataProvider()
    {
        return array(
            array(
                array(),
                array()
            ),
            array(
                array('min_text_length' => 10),
                array('validate-length', 'minimum-length-10')
            ),
            array(
                array('max_text_length' => 20),
                array('validate-length', 'maximum-length-20')
            ),
            array(
                array('min_text_length' => 10, 'max_text_length' => 20),
                array('validate-length', 'minimum-length-10', 'maximum-length-20')
            ),
        );
    }
}
