<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CheckoutAgreements\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;

/**
 * Class AgreementGrid
 * Backend Agreement grid
 */
class AgreementGrid extends Grid
{
    /**
     * First row selector
     *
     * @var string
     */
    protected $firstRowSelector = '//tr[1]/td[contains(@class, "col-name")]';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => 'input[name="name"]',
        ],
    ];
}
