<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart price rule chooser
 */
namespace Magento\CatalogRule\Block\Adminhtml\Promo\Widget;

class Chooser extends \Magento\Backend\Block\Widget\Grid\Chooser
{
    /**
     * @var string
     */
    protected $chooserUrl = '%s/promo_quote/chooser';

    /**
     * @var string
     */
    protected $type = 'catalog_rule';
}
