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

use Magento\Core\Test\Block\Title;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class for page with customer information in backend
 *
 * @package Magento\Customer\Test\Page
 */
class AdminCustomerInformation extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'admin/customer/edit';

    /**
     * @var Title
     */
    protected $_titleBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->_titleBlock = Factory::getBlockFactory()->getMagentoCoreTitle(
            $this->_browser->find('.page-title .title')
        );
    }

    /**
     * Getter for title block
     *
     * @return Title
     */
    public function getTitleBlock()
    {
        return $this->_titleBlock;
    }
} 