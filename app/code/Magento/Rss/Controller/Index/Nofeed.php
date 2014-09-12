<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Controller\Index;

class Nofeed extends \Magento\Rss\Controller\Index
{
    /**
     * Display feed not found message
     *
     * @return void
     */
    public function execute()
    {
        $this->_rssHelper->sendEmptyRssFeed($this->getResponse());
    }
}
