<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
