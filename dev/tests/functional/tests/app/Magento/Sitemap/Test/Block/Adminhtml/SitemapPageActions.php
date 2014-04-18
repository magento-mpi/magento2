<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\FormPageActions;

/**
 * Class SitemapPageActions
 * Backend sitemap edit page actions
 *
 * @package Magento\Sitemap\Test\Block\Adminhtml
 */
class SitemapPageActions extends FormPageActions
{
    /**
     * "Delete" button
     *
     * @var string
     */
    protected $deleteButton = '#delete';

    /**
     * Click on "Delete" button
     */
    public function delete()
    {
        $this->_rootElement->find($this->deleteButton)->click();
        $this->_rootElement->acceptAlert();
    }
}
