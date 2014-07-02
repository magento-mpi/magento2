<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\System\Store\Delete;

use Mtf\Client\Element;
use Mtf\Block\Form as ParentForm;
use Mtf\Client\Element\Locator;

/**
 * Class StoreForm
 * Form for Store View deletion
 */
class StoreForm extends ParentForm
{
    /**
     * Fill Backup Option in Delete Store View
     *
     * @param string $performBackup
     * @return void
     */
    public function fillForm($performBackup = "No")
    {
        $this->_rootElement->find("#store_create_backup", Locator::SELECTOR_CSS, "select")->setValue($performBackup);
    }
}
