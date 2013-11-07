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

namespace Magento\Catalog\Test\Page\Product;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class CatalogProductEdit
 * Edit product page
 */
class CatalogProductEdit extends Page
{
    /**
     * URL for product creation
     */
    const MCA = 'catalog/product/edit';

    /*
     * Selector for message block
     *
     * @var string
     */
    protected $messagesSelector = '#messages.messages .messages';

    /**
     * Messages block
     *
     * @var \Magento\Core\Test\Block\Messages
     */
    protected $messagesBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->messagesBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesSelector, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->messagesBlock;
    }
}
