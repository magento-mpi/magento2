<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Block\Adminhtml\Customer\Edit\Tab\Balance\History;

use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;
use Magento\CustomerBalance\Test\Fixture\CustomerBalance;
use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Balance history grid
 */
class Grid extends ParentGrid
{
    /**
     * More information description template
     *
     * @var string
     */
    protected $moreInformation = "By admin: admin. (%s)";

    /**
     * Customer notified mapping
     *
     * @var array
     */
    protected $customerNotified = ['Yes' => 'Notified', 'No' => 'No'];

    /**
     * Verify value in balance history grid
     *
     * @param CustomerBalance $customerBalance
     * @return bool
     */
    public function verifyCustomerBalanceGrid(CustomerBalance $customerBalance)
    {
        $moreInformation = $customerBalance->getAdditionalInfo();
        $gridRowValue = './/tr[td[contains(.,"' . abs($customerBalance->getBalanceDelta()) . '")]';
        $customerNotified = $this->customerNotified[$customerBalance->getIsCustomerNotified()];
        $gridRowValue .= ' and td[contains(.,"' .  $customerNotified . '")]';
        if ($moreInformation) {
            $gridRowValue .= ' and td["' . sprintf($this->moreInformation, $moreInformation) . '"]';
        }
        $gridRowValue .= ']';
        $this->waitForElementVisible('.headings');
        return $this->_rootElement->find($gridRowValue, Locator::SELECTOR_XPATH)->isVisible();
    }
}
