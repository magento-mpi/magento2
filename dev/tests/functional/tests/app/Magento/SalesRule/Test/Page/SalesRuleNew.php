<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Core\Test\Block\Messages;
use Magento\SalesRule\Test\Block\PromoQuoteForm;

class SalesRuleNew extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'sales_rule/promo_quote/new';

    /**
     * Sales Rule Form
     *
     * @var PromoQuoteForm
     */
    private $promoQuoteForm;

    /**
     * @var  Messages
     */
    private $messageBlock;

    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->promoQuoteForm = Factory::getBlockFactory()->getMagentoSalesRulePromoQuoteForm(
            $this->_browser->find('[id="page:main-container"]')
        );
        $this->messageBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages .messages')
        );
    }

    /**
     * @return PromoQuoteForm
     */
    public function getPromoQuoteForm()
    {
        return $this->promoQuoteForm;
    }

    /**
     * @return Messages
     */
    public function getMessageBlock()
    {
        return $this->messageBlock;
    }
}
