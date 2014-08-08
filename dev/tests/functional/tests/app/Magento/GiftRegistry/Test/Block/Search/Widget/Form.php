<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Search\Widget;

use Mtf\Block\Form as ParentForm;
use Mtf\Client\Element\Locator;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class Form
 * Gift registry search form
 */
class Form extends ParentForm
{
    /**
     * Gift Registry search type selector
     *
     * @var string
     */
    protected $searchType = '[name="search_by"]';

    /**
     * "Search" buttons selectors
     *
     * @var string
     */
    protected $searchButtons = '.giftregistry .search';

    /**
     * Gift Registry type selector
     *
     * @var string
     */
    protected $giftRegistryType = '[name="params[type_id]"]';

    /**
     * Select Gift Registry search type
     *
     * @param string $type
     * @return void
     */
    public function selectSearchType($type)
    {
        $this->_rootElement->find($this->searchType, Locator::SELECTOR_CSS, 'select')->setValue($type);
    }

    /**
     * Click "Search" button
     *
     * @return void
     */
    public function clickSearch()
    {
        $searchButtons = $this->_rootElement->find($this->searchButtons)->getElements();
        foreach ($searchButtons as $button) {
            if ($button->isVisible()) {
                $button->click();
                break;
            }
        }
    }

    /**
     * Fill Gift Registry search form
     *
     * @param CustomerInjectable $customer
     * @param string $giftRegistryType
     *
     * return void
     */
    public function fillForm($customer, $giftRegistryType)
    {
        $this->fill($customer);
        $giftRegistryTypeSelector = $this->_rootElement->find($this->giftRegistryType, Locator::SELECTOR_CSS, 'select');
        if ($giftRegistryTypeSelector->isVisible()) {
            $giftRegistryTypeSelector->setValue($giftRegistryType);
        }
    }
}
