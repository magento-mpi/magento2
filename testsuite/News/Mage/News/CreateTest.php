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
 * Creating Admin User
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class News_Mage_News_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Dashboard/p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('news');
    }

    /**
     * <p>Test navigation.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Add New News' button is present and click her.</p>
     * <p>2. Verify that the create News page is opened.</p>
     * <p>3. Verify that 'Back' button is present.</p>
     * <p>4. Verify that 'Save User' button is present.</p>
     * <p>5. Verify that 'Reset' button is present.</p>
     *
     * @test
     */
    public function navigationTest()
    {
        $this->assertTrue($this->buttonIsPresent('add_new_news'),
            'There is no "Add New News" button on the page');
        $this->clickButton('add_new_news');
        $this->assertTrue($this->checkCurrentPage('news_new'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_news'), 'There is no "Save User" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Create Admin News (all required fields are filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to News.</p>
     * <p>2.Press "Add New Wes" button.</p>
     * <p>3.Fill all required fields.</p>
     * <p>4.Press "Save News" button.</p>
     * <p>Expected result:</p>
     * <p>New user successfully saved.</p>
     * <p>Message "The News has been saved." is displayed.</p>
     *
     * @return array
     * @test
     * @depends navigationTest
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $newsData = $this->loadDataSet('News', 'generic_news');
        //Steps
        $this->NewsHelper()->createNews($newsData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_news');

        return $newsData;
    }
    public function specialCharactersInRequiredFields()
    {
      //Data
        $specialCharacters = array('news_title'  => $this->generate('string', 32, ':punct:'),
                                   'author' => $this->generate('string', 32, ':punct:'),
                                   'publish_date'  => $this->generate('string', 32, ':punct:'),
                                   'content' => $this->generate('string', 32, ':punct:'),);
        $newsData = $this->loadDataSet('News', 'generic_news', $specialCharacters);
        //Steps
        $this->NewsHelper()->createNews($newsData);
        //Verifying
        $this->assertMessagePresent('error', 'need_сorrect_date');

        return $newsData;
    }

    public function createNewSaveAndContinue()
    {
        //Data
        $newsData = $this->loadDataSet('News', 'generic_news');
        //Steps
        $this->NewsHelper()->createNewsandNotSave($newsData);
        $this->clickButton('save_news_continue');
        $this->checkCurrentPage('news_new');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_news');

        return $newsData;
    }
    /**
     * <p>Create Admin News (all fields are filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to News.</p>
     * <p>2.Press "Add New Wes" button.</p>
     * <p>3.Fill all fields.</p>
     * <p>4.Press "Save News" button.</p>
     * <p>Expected result:</p>
     * <p>New user successfully saved.</p>
     * <p>Message "The News has been saved." is displayed.</p>
     *
     * @return array
     * @test
     * @depends navigationTest
     */


    public function withAllFields()
    {
        //Data
        $newsData = $this->loadDataSet('News', 'allfields_news');
        //Steps
        $this->NewsHelper()->createNews($newsData);
        //Verifying
        $this->assertMessagePresent('success','success_saved_news');

        return $newsData;
    }

}