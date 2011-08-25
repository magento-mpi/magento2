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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeSet_Helper extends Mage_Selenium_TestCase
{

    /**
     * Action_helper method for Create Attribute Set
     *
     * @param array $attrSet Array which contains DataSet for filling of the current form
     */
    public function createAttributeSet(array $attrSet)
    {
        $this->navigate('manage_attribute_sets');
        $this->clickButton('add_new_set');
        $this->fillForm($attrSet, 'attribute_sets_grid');
        $this->addParameter('id', '0');
        $this->addParameter('attributeName', $attrSet['name']);
        $this->clickButton('save_attribute_set', TRUE);
        $this->AdminUserHelper()->defineId('edit_attribute_set');
        $this->answerOnNextPrompt($attrSet['attribute_group']['folder']);
        $this->clickButton('add_new', FALSE);
        foreach($attrSet['attribute_group']['attributes'] as $key => $value)
        {
            $this->addParameter('attributeName', $value);
            $this->addParameter('folderName', $attrSet['attribute_group']['folder']);
            $elFrom = $this->_getControlXpath('link', 'unassigned_attribute');
            $elTo = $this->_getControlXpath('link', 'group_folder');
            $this->pleaseWait('5', '5');
            $this->clickAt($elFrom, '1,1');
            $this->pleaseWait('5', '5');
            $this->clickAt($elTo, '1,1');
            $this->pleaseWait('5', '5');
            $this->mouseDownAt($elFrom, '1,1');
            $this->pleaseWait('5', '5');
            $this->mouseMoveAt($elTo, '1,-700');
            $this->pleaseWait('5', '5');
            $this->mouseUpAt($elTo, '1,-700');
        }
        $this->saveForm('save_attribute_set');
    }

    /**
     * Action_helper method for Delete Attribute Set
     *
     * @param array $attrSet Array which contains DataSet for searching attribute set
     */
    public function openAttributeSet($setName)
    {
        $this->navigate('manage_attribute_sets');
        $this->searchAndOpen($setName, TRUE);

    }
}
