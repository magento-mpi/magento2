<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract
 */
class Mage_ImportExport_Model_Import_Entity_V2_Eav_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for method getAttributeOptions()
     */
    public function testGetAttributeOptions()
    {
        $indexAttributeCode = 'gender';

        /** @var $model Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract|PHPUnit_Framework_MockObject_MockObject */
        $model = $this->getMockForAbstractClass('Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract', array(), '',
            false);

        /** @var $attributeCollection Mage_Customer_Model_Resource_Attribute_Collection */
        $attributeCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Attribute_Collection');
        $attributeCollection->addFieldToFilter(
            'attribute_code',
            array(
                'in' => array('gender', 'group_id')
            )
        );
        /** @var $attribute Mage_Customer_Model_Attribute */
        foreach ($attributeCollection as $attribute) {
            $index = ($attribute->getAttributeCode() == $indexAttributeCode) ? 'value' : 'label';
            $expectedOptions = array();
            foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                $expectedOptions[strtolower($option[$index])] = $option['value'];
            }

            $actualOptions = $model->getAttributeOptions($attribute, array($indexAttributeCode));
            $this->assertSame($expectedOptions, $actualOptions);
        }
    }
}
