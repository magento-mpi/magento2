<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Block\Adminhtml\Promo\Widget;

class Chooser extends \Magento\Backend\Block\Widget\Grid\Chooser
{
    /**
     * @var string
     */
    protected $chooserUrl = '%s/promo_quote/chooser';

    /**
     * @var string
     */
    protected $type = 'sales_rule';
}
