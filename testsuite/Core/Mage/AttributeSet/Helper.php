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
}