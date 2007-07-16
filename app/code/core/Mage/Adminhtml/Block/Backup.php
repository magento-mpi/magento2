<?
/**
 * Adminhtml backup page content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Backup extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/backup/list.phtml');
    }

    protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild('createButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => __('Create Backup'),
                    'onclick' => "window.location.href='" . Mage::getUrl('*/*/create') . "'",
                ))
        );
        $this->setChild('backupsGrid',
            $this->getLayout()->createBlock('adminhtml/backup_grid')
        );
    }

    public function getCreateButtonHtml()
    {
        return $this->getChildHtml('createButton');
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('backupsGrid');
    }
}
