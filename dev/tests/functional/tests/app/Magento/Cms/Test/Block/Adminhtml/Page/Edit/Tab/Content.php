<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Page\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element\Locator;

/**
 * Class Content
 * Content Tab
 */
class Content extends Tab
{
    /**
     * System Variable block selector
     *
     * @var string
     */
    protected $systemVariableBlock = "./ancestor::body//div[div[@id='variables-chooser']]";

    /**
     * Insert Variable button selector
     *
     * @var string
     */
    protected $addVariableButton = ".add-variable";

    /**
     * Clicking in content tab 'Insert Variable' button
     *
     * @return void
     */
    public function clickInsertVariable()
    {
        $addVariableButton = $this->_rootElement->find($this->addVariableButton);
        if ($addVariableButton->isVisible()) {
            $addVariableButton->click();
        }
    }

    /**
     * Getter for wysiwyg config
     *
     * @return \Magento\Cms\Test\Block\Adminhtml\Wysiwyg\Config
     */
    public function getWysiwygConfig()
    {
        $config = $this->blockFactory->create(
            'Magento\Cms\Test\Block\Adminhtml\Wysiwyg\Config',
            ['element' => $this->_rootElement->find($this->systemVariableBlock, Locator::SELECTOR_XPATH)]
        );

        return $config;
    }
}
