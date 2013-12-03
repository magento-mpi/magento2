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

namespace Magento\Rma\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Returns
 * View returns page
 *
 * @package Magento\Rma\Test\Page
 */
class Returns extends Page
{
    /**
     * URL for returns page
     */
    const MCA = 'sales/guest/returns';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get global messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('.messages .messages', Locator::SELECTOR_CSS)
        );
    }
}