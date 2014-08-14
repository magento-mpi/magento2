<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit;

use Mtf\Block\Form;

/**
 * Class Sharing
 * Backend sharing gift registry form
 */
class Sharing extends Form
{
    /**
     * Share Gift Registry button selector
     *
     * @var string
     */
    protected $shareGiftRegistry = '[type="submit"]';

    /**
     * Click share gift registry
     *
     * @return void
     */
    public function shareGiftRegistry()
    {
        $this->_rootElement->find($this->shareGiftRegistry)->click();
    }

    /**
     * Fill Sharing Information form
     *
     * @param array $sharingInfo
     * @return void
     */
    public function fillForm(array $sharingInfo)
    {
        $mapping = $this->dataMapping($sharingInfo);
        $this->_fill($mapping);
    }
}
