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
 * Test adding new Template.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_NewsletterAdmin_AddTemplateTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Newsletter</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('newsletter_templates');
    }
    
    /**
     * <p>Test navigation.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Add New Template' button is present and click it.</p>
     * <p>2. Verify that the New Newsletter Template page is opened.</p>
     * <p>3. Verify that 'Back' button is present.</p>
     * <p>4. Verify that 'Reset Template' button is present.</p>
     * <p>5. Verify that 'Convert to Plain Text' button is present.</p>
     * <p>3. Verify that 'Preview Template' button is present.</p>
     * <p>3. Verify that 'Save Template' button is present.</p>
     * <p>1. Verify that 'Show / Hide Editor' button is present and click it.</p>
     * <p>3. Verify that 'Insert Widget...' button is present.</p>
     * <p>4. Verify that 'Insert Image...' button is present.</p>
     * <p>5. Verify that 'Insert Variable...' button is present.</p>
     *
     * @test
    */
    public function navigation()
    {
        $this->assertTrue($this->controlIsPresent('button', 'add_new_template'),
                'There is no "Add New Template" button on the page'); 
        $this->clickButton('add_new_template');
        $this->assertTrue($this->controlIsPresent('button', 'back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'convert_to_plain_text'), 'There is no "Convert to Plain Text" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'preview_template'), 'There is no "Preview Template" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_template'), 'There is no "Save Template" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'show_hide_editor'),
                'There is no "Show / Hide Editor" button on the page'); 
        $this->clickButton('show_hide_editor', false);
        $this->assertTrue($this->controlIsPresent('button', 'insert_widget'), 'There is no "Insert Widget..." button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'insert_image'), 'There is no "Insert Image..." button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'insert_variable'), 'There is no "Insert Variable..." button on the page');
    }
    
        /**
     * <p>Add Template. Fill in only required fields.</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add New Template' button.</p>
     * <p>2. Fill in required fields.</p>
     * <p>3. Click 'Save Template' button.</p>
     * <p>Expected result:</p>
     * <p>Template is created.</p>
     * <p>Success Message is displayed</p>
     *
     * @test
     * @TestlinkId	TL-MAGE-3618
     */
    public function withRequiredFieldsOnly()
    {
        #if (is_string($data)) {
       #     $data = $this->loadData($data);
      #  }
     #   $data = $this->arrayEmptyClear($data);
        
        //Steps
        $this->clickButton('add_new_template');
        $this->fillForm('newsletter_add_template');
        $this->saveForm('save_template');
        //Verifying that list of templates displayed
      #  $this->assertMessagePresent('success', 'success_saved_store');
        $this->validatePage('newsletter_templates');
    }
    
}
?>
