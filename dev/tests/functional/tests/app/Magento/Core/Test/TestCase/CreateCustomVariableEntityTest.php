<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Core\Test\Fixture\SystemVariable;
use Magento\Core\Test\Page\Adminhtml\SystemVariableIndex;
use Magento\Core\Test\Page\Adminhtml\SystemVariableNew;

/**
 * Test Creation for CreateCustomVariableEntity
 *
 * Test Flow:
 * Steps:
 * 1. Login to backend.
 * 2. Navigate to System->Other Settings->Custom Variables.
 * 3. Click on 'Add new variable' button.
 * 4. Fill in all data according to data set.
 * 5. Click 'Save' button.
 * 6. Perform all asserts.
 *
 * @group Variables_(PS)
 * @ZephyrId MAGETWO-23293
 */
class CreateCustomVariableEntityTest extends Injectable
{
    /**
     * Custom System Variable grid page
     *
     * @var SystemVariableIndex
     */
    protected $systemVariableIndexPage;

    /**
     * Custom System Variable new and edit page
     *
     * @var SystemVariableNew
     */
    protected $systemVariableNewPage;

    /**
     * Injection data
     *
     * @param SystemVariableIndex $systemVariableIndex
     * @param SystemVariableNew $systemVariableNew
     * @return void
     */
    public function __inject(
        SystemVariableIndex $systemVariableIndex,
        SystemVariableNew $systemVariableNew
    ) {
        $this->systemVariableIndexPage = $systemVariableIndex;
        $this->systemVariableNewPage = $systemVariableNew;
    }

    /**
     * Delete Custom System Variable Entity test
     *
     * @param SystemVariable $systemVariable
     * @return void
     */
    public function test(SystemVariable $systemVariable)
    {
        // Steps
        $this->systemVariableIndexPage->open();
        $this->systemVariableIndexPage->getGridPageActions()->addNew();
        $this->systemVariableNewPage->getSystemVariableForm()->fill($systemVariable);
        $this->systemVariableNewPage->getFormPageActions()->save();
    }
}
