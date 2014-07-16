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
        $this->getResponse()->setHeader(
            'HTTP/1.1',
            '404 Not Found'
        )->setHeader(
            'Status',
            '404 File not found'
        )->setHeader(
            'Content-Type',
            'text/plain; charset=UTF-8'
        )->setBody(
            __('There was no RSS feed enabled.')
        );
    }
}
