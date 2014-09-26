<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Helper;

/**
 * Order rss helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Setting headers to response for sending empty rss
     *
     * @param \Magento\Framework\App\ResponseInterface $response
     * @return void
     */
    public function sendEmptyRssFeed(\Magento\Framework\App\ResponseInterface $response)
    {
        $response->setHeader(
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
