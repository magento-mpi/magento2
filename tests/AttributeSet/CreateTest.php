<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @TODO
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeSet_CreateTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_attribute_sets');
        $this->assertTrue($this->checkCurrentPage('manage_attribute_sets'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }

    /**
     * <p>TL-MAGE-74:Attribute Set creation - based on Default</p>
     * <p>Steps</p>
     * <p>1. Click button "Add New Set"</p>
     * <p>2. Fill in fields</p>
     * <p>3. Click button "Save Attribute Set"</p>
     * <p>Expected result</p>
     * <p>Received the message on successful completion of the attribute set creation</p>
     * @test
     */
    public function basedOnDefault()
    {
        //Data
        $attributeSetData = $this->loadData('attribute_set_default', null, 'set_name');
        //Steps
        $this->attributeSetHelper()->createAttributeSet($attributeSetData);
        //Verifying
        $this->assertTrue($this->successMessage('success_attribute_set_saved'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attribute_sets'),
                'After successful attribute set creation should be redirected to Edit Attribute Set page');
        return $attributeSetData;
    }

    /**
     * <p>Attribute Set creation - based on Custom</p>
     * <p>Preconditions:</p>
     * <p>Attribute set created based on default</p>
     * <p>Steps</p>
     * <p>1. Click button "Add New Set"</p>
     * <p>2. Fill in fields - choose existing Attribute Set in "Based On" field</p>
     * <p>3. Click button "Save Attribute Set"</p>
     * <p>Expected result</p>
     * <p>Received the message on successful completion of the attribute set creation</p>
     *
     * @depends basedOnDefault
     * @test
     */
    public function basedOnCustom($attributeSetData)
    {
        //Data
        $attributeSetDataCustom = $this->loadData('attribute_set_default',
                array('based_on' => $attributeSetData['set_name']), 'set_name');
        //Steps
        $this->attributeSetHelper()->createAttributeSet($attributeSetDataCustom);
        //Verifying
        $this->assertTrue($this->successMessage('success_attribute_set_saved'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attribute_sets'),
                'After successful attribute set creation should be redirected to Edit Attribute Set page');
    }

    /**
     * <p>TL-MAGE-76:Attribute Set creation - existing name</p>
     * <p>Preconditions:</p>
     * <p>Attribute set created based on default</p>
     * <p>Steps</p>
     * <p>1. Click button "Add New Set"</p>
     * <p>2. Fill in fields - type existing Attribute Set name in "Name" field</p>
     * <p>3. Click button "Save Attribute Set"</p>
     * <p>Expected result</p>
     * <p>Received error message "Attribute set with the "attrSet_name" name already exists."</p>
     *
     * @depends basedOnDefault
     * @test
     */
    public function withNameThatAlreadyExists($attributeSetData)
    {
        //Data
        $attributeSetDataCustom = $this->loadData('attribute_set_default',
                array('set_name' => $attributeSetData['set_name']));
        //Steps
        $this->attributeSetHelper()->createAttributeSet($attributeSetData);
        //Verifying
        $this->addParameter('attributeSetName', $attributeSetData['set_name']);
        $this->assertTrue($this->errorMessage('error_attribute_set_exist'), $this->messages);
    }

    /**
     * <p>TL-MAGE-75:Attribute Set creation - empty name</p>
     * <p>Steps</p>
     * <p>1. Click button "Add New Set"</p>
     * <p>2. Click button "Save Attribute Set"</p>
     * <p>Expected result</p>
     * <p>Received error message "This is a required field."</p>
     *
     * @depends basedOnDefault
     * @test
     */
    public function withEmptyName()
    {
        //Data
        $attributeSetData = $this->loadData('attribute_set_default',
                array('set_name' => '%noValue%'));
        //Steps
        $this->attributeSetHelper()->createAttributeSet($attributeSetData);
        //Verifying
        $this->addFieldIdToMessage('field', 'set_name');
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    /**
     * <p>Creating Attribute Set with long values in required fields</p>
     * <p>Steps</p>
     * <p>1. Click button "Add New Set"</p>
     * <p>2. Fill in "Name" field by long values;</p>
     * <p>3. Click button "Save Attribute Set"</p>
     * <p>Expected result:</p>
     * <p>Received the message on successful completion of the attribute set creation</p>
     *
     * @depends basedOnDefault
     * @test
     */
    public function withLongValues()
    {
        //Data
        $attributeSetData = $this->loadData('attribute_set_default',
                array('set_name' => $this->generate('string', 255, ':alnum:')));
        $attributeSetSearch = $this->loadData('search_attribute_set',
                array('set_name' => $attributeSetData['set_name']));
        //Steps
        $this->attributeSetHelper()->createAttributeSet($attributeSetData);
        //Verifying
        $this->assertTrue($this->successMessage('success_attribute_set_saved'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attribute_sets'),
                'After successful attribute set creation should be redirected to Manage Attribute Set page');
        //Steps
        $this->attributeSetHelper()->openAttributeSet($attributeSetSearch);
        $xpath = $this->_getControlXpath('field', 'set_name');
        $setValue = $this->getValue($xpath);
        $this->assertEquals($setValue, $attributeSetData['set_name'], 'Attribute name should be equal');
    }

    /**
     * <p>TL-MAGE-77:Add user product attributes</p>
     * <p>Preconditions</p>
     * <p>Product Attribute created</p>
     * <p>Steps</p>
     * <p>1. Click button "Add New Set"</p>
     * <p>2. Fill in "Name" field</p>
     * <p>3. Click button "Add New" in Groups</p>
     * <p>4. Fill in "Name" field</p>
     * <p>5. Assign user product  Attributes* to "User Attributes' group</p>
     * <p>6. Click button "Save Attribute Set"</p>
     * <p>Expected result:</p>
     * <p>Received the message on successful completion of the attribute set creation</p>
     *
     * @test
     */
    public function addUserProductAttributes()
    {
        //Create Attributes
        $this->navigate('manage_attributes');
        $this->assertTrue($this->checkCurrentPage('manage_attributes'), 'Wrong page is opened');
        //Attributes Data
        $attrData = $this->loadData('product_attributes', NULL,
                        array('attribute_code', 'admin_title'));
        $attributeSetData = $this->loadData('attribute_set_default', null, 'set_name');
        //Steps
        foreach ($attrData as $key => $value) {
            $this->productAttributeHelper()->createAttribute($value);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attributes'),
                'After successful attribute creation should be redirected to Manage Attributes page');
        }
        $this->assertPreConditions();
        //Steps
        $this->clickButton('add_new_set');
        $this->fillForm($attributeSetData, 'attribute_sets_grid');
        $this->addParameter('attributeName', $attributeSetData['set_name']);
        $this->clickButton('save_attribute_set');
        $this->attributeSetHelper()->addNewGroup(array('new_groups' => 'test_group'));
        foreach ($attrData as $key => $value) {
            $this->attributeSetHelper()->addAttributeToSet(array('test_group' => $value['attribute_code']));
        }
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertTrue($this->successMessage('success_attribute_set_saved'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attribute_sets'),
                'After successful attribute set creation should be redirected to Manage Attribute Set page');
    }
}
