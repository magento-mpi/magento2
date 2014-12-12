<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
