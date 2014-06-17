<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Block\Iframe;

class ExtendsAbstractIframe extends \Magento\Pbridge\Block\Iframe\AbstractIframe
{
    /**
     * Default iframe height
     *
     * @var string
     */
    protected $_iframeHeight = '500';

    /**
     * @inheritdoc
     */
    public function getSourceUrl()
    {
        return 'source_url';
    }
}
