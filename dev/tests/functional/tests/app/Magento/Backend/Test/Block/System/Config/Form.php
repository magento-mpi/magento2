<?php
/**
 * Store configuration edit form
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Test\Block\System\Config;

use \Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use \Mtf\Factory\Factory;

class Form extends Block
{
    /**
     * Group block
     *
     * @var string
     */
    protected $groupBlock = '//legend[contains(text(), "%s")]/../..';

    /**
     * Save button
     *
     * @var string
     */
    protected $saveButton = '//button[@title="Save Config"]';

    /**
     * Retrieve store configuration form group
     *
     * @param string $name
     * @return Form\Group
     */
    public function getGroup($name)
    {
        $blockFactory = Factory::getBlockFactory();
        $element = $this->_rootElement->find(
            sprintf($this->groupBlock, $name), Locator::SELECTOR_XPATH
        );
        return $blockFactory->getMagentoBackendSystemConfigFormGroup($element);
    }

    /**
     * Save store configuration
     */
    public function save()
    {
        $this->_rootElement->find($this->saveButton, Locator::SELECTOR_XPATH)->click();
    }
}
