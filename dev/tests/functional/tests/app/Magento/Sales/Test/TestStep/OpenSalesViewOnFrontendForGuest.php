<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Sales\Test\Page\SalesGuestForm;
use Magento\Sales\Test\Fixture\OrderInjectable;

class OpenSalesViewOnFrontendForGuest implements TestStepInterface
{
    /**
     * Cms index page.
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Sales guest page.
     *
     * @var SalesGuestForm
     */
    protected $salesGuestForm;

    /**
     * Fixture order.
     *
     * @var OrderInjectable
     */
    protected $order;

    /**
     * @constructor
     * @param CmsIndex $cmsIndex
     * @param SalesGuestForm $salesGuestForm
     * @param OrderInjectable $order
     */
    public function __construct(CmsIndex $cmsIndex, SalesGuestForm $salesGuestForm, OrderInjectable $order)
    {
        $this->cmsIndex = $cmsIndex;
        $this->salesGuestForm = $salesGuestForm;
        $this->order = $order;
    }

    /**
     * Run step.
     *
     * @return void
     */
    public function run()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getFooterBlock()->clickLink('Orders and Returns');
        $this->salesGuestForm->getSearchForm()->fill($this->order);
        $this->salesGuestForm->getSearchForm()->submit();
    }
}
