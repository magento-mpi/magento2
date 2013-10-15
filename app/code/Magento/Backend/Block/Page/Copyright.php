<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Backend\Block\Page;

/**
 * Copyright footer block
 */
class Copyright extends \Magento\Backend\Block\Template
{
    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        echo __('Magento is an eBay Inc. company. Copyright&copy; %1 Magento, Inc. All rights reserved.', date('Y'));
    }
}
