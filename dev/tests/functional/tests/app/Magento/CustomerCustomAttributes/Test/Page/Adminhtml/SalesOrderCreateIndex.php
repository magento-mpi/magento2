<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Page\Adminhtml;

/**
 * Class SalesOrderCreateIndex
 */
class SalesOrderCreateIndex extends \Magento\Sales\Test\Page\SalesOrderCreateIndex
{
    const MCA = 'sales/order_create';

    protected $_blocks = [
        'createBlock' => [
            'name' => 'createBlock',
            'class' => 'Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Order\Create',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Order\Create
     */
    public function getCreateBlock()
    {
        return $this->getBlockInstance('createBlock');
    }
}
