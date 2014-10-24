<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reminder\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Reminder\Test\Page\Adminhtml\ReminderIndex;
use Magento\Reminder\Test\Page\Adminhtml\ReminderView;
use Mtf\Fixture\FixtureFactory;
use Magento\Reminder\Test\Fixture\Reminder;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Preconditions:
 * 1. Create customer1 and customer2
 * 2. Create Product with price 100
 *
 * Steps:
 * 1. Login to backend
 * 2. Go to Marketing > Email Reminders
 * 3. Create new reminder
 * 4. Fill data from dataSet
 * 5. Save Reminder
 * 6. Perform all assertions
 *
 * @group Email_Reminder_(MX)
 * @ZephyrId MAGETWO-29790
 */
class CreateAutomatedEmailReminderRuleTest extends Injectable
{
    /**
     * Email Reminder grid page.
     *
     * @var ReminderIndex
     */
    protected $reminderIndex;

    /**
     * Email Reminder view page.
     *
     * @var ReminderView
     */
    protected $reminderView;

    /**
     * Prepare data.
     *
     * @param ReminderIndex $reminderIndex
     * @param ReminderView $reminderView
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(ReminderIndex $reminderIndex, ReminderView $reminderView, FixtureFactory $fixtureFactory)
    {
        $this->reminderIndex = $reminderIndex;
        $this->reminderView = $reminderView;

        $product = $fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => '100_dollar_product']);
        $product->persist();
        return ['product' => $product];
    }

    /**
     * Inject data.
     *
     * @param CustomerInjectable $customer1
     * @param CustomerInjectable $customer2
     * @return array
     */
    public function __inject(CustomerInjectable $customer1, CustomerInjectable $customer2)
    {
        $customer1->persist();
        $customer2->persist();

        return [
            'customer1' => $customer1,
            'customer2' => $customer2
        ];
    }

    /**
     * Run create automated email reminder rule test.
     *
     * @param Reminder $reminder
     * @reutrn void
     */
    public function test(Reminder $reminder)
    {
        $this->reminderIndex->open();
        $this->reminderIndex->getPageActionsBlock()->addNew();
        $this->reminderView->getReminderForm()->fill($reminder);
        $this->reminderView->getPageMainActions()->save();
    }
}
