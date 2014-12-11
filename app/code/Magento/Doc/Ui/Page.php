<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Doc\Ui;

class Page extends Widget
{
    /**
     * Render HTML page content
     * (add doctype)
     *
     * @return string
     */
    public function _toHtml()
    {
        return '<!doctype html>' . parent::_toHtml();
    }
}
