<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsPolls
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Poll creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CmsPolls_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to CMS -> Polls</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('poll_manager');
    }

    /**
     * <p>Delete a Poll</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3222
     */
    public function deleteNewPoll()
    {
        //Data
        $pollData = $this->loadDataSet('CmsPoll', 'poll_open');
        $searchPollData = $this->loadDataSet('CmsPoll', 'search_poll',
            array('filter_question' => $pollData['poll_question']));
        //Steps
        $this->cmsPollsHelper()->createPoll($pollData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_poll');
        //Steps
        $this->cmsPollsHelper()->deletePoll($searchPollData);
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_poll');
    }
}