<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AttributeSet
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_AttributeSet_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Create Attribute Set
     *
     * @param array $attrSet Array which contains DataSet for filling of the current form
     */
    public function createAttributeSet(array $attrSet)
    {
        $groups = (isset($attrSet['new_groups'])) ? $attrSet['new_groups'] : array();
        $associatedAttr = (isset($attrSet['associated_attributes'])) ? $attrSet['associated_attributes'] : array();

        $this->clickButton('add_new_set');
        $this->fillForm($attrSet);
        $this->addParameter('elementTitle', $attrSet['set_name']);
        $this->saveForm('save_attribute_set');

        $this->addNewGroup($groups);
        $this->addAttributeToSet($associatedAttr);
        if ($groups || $associatedAttr) {
            $waitCondition =
                array($this->_getMessageXpath('general_error'), $this->_getMessageXpath('general_validation'),
                      $this->_getControlXpath('fieldset', 'attribute_sets_grid',
                          $this->getUimapPage('admin', 'manage_attribute_sets')));
            $this->clickButton('save_attribute_set', false);
            $this->waitForElement($waitCondition);
            $this->validatePage();
        }
    }

    /**
     * Add new group to attribute set
     *
     * @param mixed $attrGroup Array or String (data divided by comma)
     *                         which contains DataSet for creating folder of attributes
     */
    public function addNewGroup($attrGroup)
    {
        if (is_string($attrGroup)) {
            $attrGroup = explode(',', $attrGroup);
            $attrGroup = array_map('trim', $attrGroup);
        }
        foreach ($attrGroup as $value) {
            $this->addParameter('folderName', $value);
            if (!$this->controlIsPresent('link', 'group_folder')) {
                $this->clickButton('add_group', false);
                $this->alertText($value);
                $this->acceptAlert();
            }
        }
    }

    /**
     * Add attribute to attribute Set
     *
     * @param array $attributes Array which contains DataSet for filling folder of attribute set
     */
    public function addAttributeToSet(array $attributes)
    {
        foreach ($attributes as $groupName => $attributeCode) {
            if ($attributeCode == '%noValue%') {
                continue;
            }
            if (is_string($attributeCode)) {
                $attributeCode = explode(',', $attributeCode);
                $attributeCode = array_map('trim', $attributeCode);
            }
            $this->addParameter('folderName', $groupName);
            if (!$this->controlIsPresent('link', 'group_folder')) {
                $this->addNewGroup($groupName);
            }
            $moveToElement = $this->getControlElement('link', 'group_folder');
            $moveToElement->click();
            foreach ($attributeCode as $value) {
                $this->addParameter('attributeName', $value);
                if (!$this->controlIsPresent('link', 'unassigned_attribute')) {
                    $this->fail("Attribute with title '$value' does not exist");
                }
                $moveElement = $this->getControlElement('link', 'unassigned_attribute');
                $moveElement->click();
                $this->moveto($moveElement);
                $this->buttondown();
                $this->moveto($moveToElement);
                $availableElement = $this->elementIsPresent("//li[div[./a/span='$groupName']]//li/div");
                if ($availableElement) {
                    $this->moveto($availableElement);
                } else {
                    $this->moveto($this->getElement("//*[a/span='$groupName']"));
                }
                $this->buttonup();
                $this->assertTrue($this->controlIsPresent('link', 'attribute_in_group'),
                    'Attribute "' . $value . '" is not assigned to group "' . $groupName . '"');
            }
        }
    }

    /**
     * Open Attribute Set
     *
     * @param string|array $setName
     */
    public function openAttributeSet($setName = 'Default')
    {
        if (is_array($setName) and isset($setName['set_name'])) {
            $setName = $setName['set_name'];
        }
        $this->addParameter('elementTitle', $setName);
        $searchData = $this->loadDataSet('AttributeSet', 'search_attribute_set', array('set_name' => $setName));

        if ($this->getCurrentPage() !== 'manage_attribute_sets') {
            $this->navigate('manage_attribute_sets');
        }
        $this->searchAndOpen($searchData, 'attribute_sets_grid');
    }

    /**
     * Verifies attributes are assigned or not to attribute set
     *
     * @param array $attributes
     * @param bool $isAssigned
     */
    public function verifyAttributeAssignment(array $attributes, $isAssigned = true)
    {
        if (empty($attributes)) {
            $this->fail('Array with attributes is empty');
        }
        foreach ($attributes as $attribute) {
            $this->addParameter('attributeName', $attribute);
            if ($isAssigned) {
                if (!$this->controlIsPresent('link', 'group_attribute')) {
                    $this->addVerificationMessage("Attribute with code '$attribute' is not assigned to attribute set");
                }
            } else {
                if (!$this->controlIsPresent('link', 'unassigned_attribute')) {
                    $this->addVerificationMessage("Attribute with code '$attribute' is assigned to attribute set");
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Delete attribute from attribute Set
     *
     * @param array $attributes Array which contains attributes for unassignment
     * @param bool $isVerifyConfirmation Verification for alert message
     */
    public function unassignAttributeFromSet(array $attributes, $isVerifyConfirmation = false)
    {
        foreach ($attributes as $attributeCode) {
            $this->addParameter('attributeName', $attributeCode);
            $unassignedGroup = $this->getControlElement('pageelement', 'unassigned_placeholder');
            $assignedAttribute = $this->getControlElement('link', 'group_attribute');
            $this->focusOnElement($unassignedGroup);
            $this->clickControl('link', 'group_attribute', false);
            $this->moveto($assignedAttribute);
            $this->buttondown();
            $this->focusOnElement($this->waitForControl('pageelement', 'unassigned_placeholder'));
            $this->moveto($this->getControlElement('pageelement', 'unassigned_placeholder'));
            $this->buttonup();
            if ($this->alertIsPresent()) {
                $text = $this->alertText();
                $this->acceptAlert();
                $this->assertFalse($this->controlIsPresent('link', 'unassigned_attribute'));
                if ($isVerifyConfirmation
                    && $text != $this->getCurrentUimapPage()->findMessage('remove_system_attribute')
                ) {
                    $this->addVerificationMessage('The alert text is incorrect: ' . $text);
                }
            } else {
                $this->assertTrue($this->controlIsPresent('link', 'unassigned_attribute'));
            }
        }
    }

    /**
     * Delete group from attribute set
     *
     * @param array $attributeGroup Array which contains groups to delete
     * @param bool $isCorrectConfirm Verification for alert message
     */
    public function deleteGroup($attributeGroup, $isCorrectConfirm = true)
    {
        foreach ($attributeGroup as $value) {
            $this->addParameter('folderName', $value);
            if ($this->controlIsPresent('link', 'group_folder')) {
                $this->clickControl('link', 'group_folder', false);
                $this->clickButton('delete_group', false);
                if ($this->alertIsPresent()) {
                    $text = $this->alertText();
                    $this->acceptAlert();
                    if ($isCorrectConfirm) {
                        if ($text != $this->getCurrentUimapPage()->findMessage('delete_group')) {
                            $this->addVerificationMessage('The alert text is incorrect: ' . $text);
                        }
                    }
                }
            } else {
                $this->addVerificationMessage('Group ' . $value . ' does not exist');
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}
