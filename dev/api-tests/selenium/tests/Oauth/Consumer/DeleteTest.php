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
 * Test deleting consumer from Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Oauth_Consumer_DeleteTest extends Mage_Selenium_TestCase
{
    /*
      * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> oAuth -> Consumers</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('oauth_consumers');
        $this->addParameter('id', '0');
    }

    /**
     * <p>Delete consumer.</p>
     * <p>Preconditions: Create Consumer</p>
     * <p>Steps:</p>
     * <p>1. Search and open consumer.</p>
     * <p>2. Click 'Delete' button.</p>
     * <p>Expected result:</p>
     * <p>Consumer is deleted.</p>
     * <p>Success Message is displayed.</p>
     *
     * @test
     */

    //Failed because https://jira.magento.com/browse/APIA-199

    public function deleteConsumer()
    {
        //Data
        $consumerData = $this->loadData('generic_consumer');
        //Preconditions
        $this->oauthHelper()->createConsumer($consumerData);
        $this->assertMessagePresent('success', 'success_saved_consumer');
        //Steps
        $this->addParameter('consumer_search_name', $consumerData['consumer_name']);
        $this->oauthHelper()->openConsumer(array('name' => $consumerData['consumer_name']));
        $this->clickButtonAndConfirm('delete_consumer', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_consumer');
    }
}
