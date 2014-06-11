<?php
/**
 * URL rewrite grid actions
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Test\Block\Adminhtml;

use Mtf\Block\Block;

/**
 * Class Actions
 * Action block
 */
class Actions extends Block
{
    /**
     * Add button
     *
     * @var string
     */
    protected $addNewButton = '#add';

    /**
     * Add new URL rewrite
     */
    public function addNewUrlRewrite()
    {
        $this->_rootElement->find($this->addNewButton)->click();
    }
}
