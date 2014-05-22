<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Block\Adminhtml\Edit;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;

/**
 * Class BalanceHistoryGrid
 * Balance history grid
 */
class BalanceHistoryGrid extends Grid
{
    /**
     * More information description template
     *
     * @var string
     */
    private $moreInformation = "By admin: admin. (%s)";

    /**
     * Search in balance history grid
     *
     * @param string $balance
     * @param string $notified
     * @param string $moreInformation
     * @return bool
     */
    public function isInCustomerBalanceGrid($balance, $notified, $moreInformation)
    {
        $gridRowValue = './/tr[td[contains(.,"' . abs($balance) . '")]';
        $gridRowValue .= ' and td[contains(.,"' . $notified . '")]';
        if ($moreInformation) {
            $gridRowValue .= ' and td["' . sprintf($this->moreInformation, $moreInformation) . '"]';
        }
        $gridRowValue .= ']';
        $this->waitForElementVisible('.headings');
        return $this->_rootElement->find($gridRowValue, Locator::SELECTOR_XPATH)->isVisible();
    }
}