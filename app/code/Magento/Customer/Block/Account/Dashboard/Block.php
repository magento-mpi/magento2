<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account\Dashboard;

class Block extends \Magento\View\Element\Template
{
    /**
     * Account dashboard block constructor.
     *
     * @param \Magento\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }
}
