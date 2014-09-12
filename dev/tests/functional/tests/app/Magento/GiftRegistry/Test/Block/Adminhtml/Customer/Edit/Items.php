<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;

/**
 * Class Items
 * Backend items gift registry grid
 */
class Items extends Grid
{
    /**
     * Selector for row item
     *
     * @var string
     */
    protected $rowSelector = './/tr[td[contains(.,"%s")]]';

    /**
     * Selector for qty value input
     *
     * @var string
     */
    protected $qtySelector = '[name$="[qty]"]';

    /**
     * Selector for action value input
     *
     * @var string
     */
    protected $actionSelector = '[name$="[action]"]';

    /**
     * Selector for update items and qty's button
     *
     * @var string
     */
    protected $submit = '[data-ui-id="giftregistry-customer-edit-form-update-button"]';

    /**
     * Search and update giftregistry item
     *
     * @param array $productProperties
     * @return void
     */
    public function searchAndUpdate(array $productProperties)
    {
        $row = $this->_rootElement->find(
            sprintf($this->rowSelector, $productProperties['name']),
            Locator::SELECTOR_XPATH
        );
        $row->find($this->qtySelector)->setValue($productProperties['qty']);
        $row->find($this->actionSelector, Locator::SELECTOR_CSS, 'select')->setValue($productProperties['action']);
        $this->_rootElement->find($this->submit)->click();
    }
}
