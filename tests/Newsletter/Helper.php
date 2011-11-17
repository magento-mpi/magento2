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
class Newsletter_Helper extends Mage_Selenium_TestCase
{

    /**
     * Subscribe to newsletter
     *
     * @param array $subscribeData
     * @param boolean $willChangePage
     */
    public function frontSubscribe($email)
    {
        $subscribeData = array('sign_up_newsletter' => $email);
        $this->fillForm($subscribeData);
        $this->saveForm('subscribe');
    }

    /**
     * Perform a mass action with newsletter subscribers
     *
     * @param string $action Mass action from data set to perform, e.g. 'unsubscribe'|'delete'
     * @param array $searchData
     */
    public function massAction($action, $searchDataSet)
    {
        $actions = $this->loadData('mass_action_nl_subscribers');
        $actionName = $actions[strtolower($action)];
        foreach ($searchDataSet as $searchData) {
            $this->searchAndChoose($searchData);
        }
        $this->addParameter('qtyOfRecords', count($searchDataSet));
        $this->fillForm(array('subscribers_massaction' => $actionName));
        $this->clickButton('submit');
    }

    /**
     * Perform a mass action with newsletter subscribers
     *
     * @param string $status Status from data set to check, e.g. 'subscribed'|'unsubscribed'
     * @param array $searchData
     * @return boolean. True if $searchData with $status status is found. False otherwise.
     */
    public function checkStatus($status, $searchData)
    {
        $statuses = $this->loadData('subscriber_status');
        $statusName = $statuses[strtolower($status)];
        $searchData['filter_status'] = $statusName;
        return is_null($this->search($searchData)) ? false : true;
    }

}
