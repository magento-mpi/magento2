<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class CustomerEdit
 * Customer information page in backend
 *
 * @package Magento\Customer\Test\Page
 */
class CustomerEdit extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'customer/edit';

    /**
     * @var string
     */
    protected $titleBlock = '.page-title .title';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Getter for title block
     *
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return Factory::getBlockFactory()->getMagentoThemeHtmlTitle(
            $this->_browser->find($this->titleBlock, Locator::SELECTOR_CSS)
        );
    }
} 