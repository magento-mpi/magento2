<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class TemplatePreview
 * TemplatePreview page
 */
class TemplatePreview extends BackendPage
{
    const MCA = 'newsletter/template/preview';

    protected $_blocks = [
        'content' => [
            'name' => 'content',
            'class' => 'Magento\Newsletter\Test\Block\Adminhtml\Template\Preview',
            'locator' => 'body',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Newsletter\Test\Block\Adminhtml\Template\Preview
     */
    public function getContent()
    {
        return $this->getBlockInstance('content');
    }
}
