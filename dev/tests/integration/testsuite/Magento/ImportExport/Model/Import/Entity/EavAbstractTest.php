<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\ImportExport\Model\Import\Entity\AbstractEav
 */
namespace Magento\ImportExport\Model\Import\Entity;

class EavAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Model object which used for tests
     *
     * @var \Magento\ImportExport\Model\Import\Entity\AbstractEav|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * Create all necessary data for tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = $this->getMockForAbstractClass(
            'Magento\ImportExport\Model\Import\Entity\AbstractEav',
            array(),
            '',
            false
        );
    }

    /**
     * Test for method getAttributeOptions()
     */
    public function testGetAttributeOptions()
    {
        $indexAttributeCode = 'gender';

        /** @var $attributeCollection \Magento\Customer\Model\Resource\Attribute\Collection */
        $attributeCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Model\Resource\Attribute\Collection'
        );
        $attributeCollection->addFieldToFilter(
            'attribute_code',
            array('in' => array($indexAttributeCode, 'group_id'))
        );
        /** @var $attribute \Magento\Customer\Model\Attribute */
        foreach ($attributeCollection as $attribute) {
            $index = $attribute->getAttributeCode() == $indexAttributeCode ? 'value' : 'label';
            $expectedOptions = array();
            foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                $expectedOptions[strtolower($option[$index])] = $option['value'];
            }
            $actualOptions = $this->_model->getAttributeOptions($attribute, array($indexAttributeCode));
            sort($expectedOptions);
            sort($actualOptions);
            $this->assertEquals($expectedOptions, $actualOptions);
        }
    }
}
