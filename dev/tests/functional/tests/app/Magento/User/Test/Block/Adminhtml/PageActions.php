<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml;


use Mtf\Block\Block;

class PageActions extends Block {
    protected $saveRole = "[data-ui-id$=savebutton]";

    /**
     * Click on Save Role button.
     */
    public function save(){
        $this->_rootElement->find($this->saveRole)->click();
    }
} 