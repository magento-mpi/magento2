<?php
/**
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Rating_Edit_Tab_Options extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('ratings/options.phtml');
    }

    public function toHtml()
    {
        if( !Mage::registry('rating_data') ) {
            $this->assign('options', false);
            return parent::toHtml();
        }

        $collection = Mage::getModel('rating/rating_option')
            ->getResourceCollection()
            ->addRatingFilter(Mage::registry('rating_data')->getId())
            ->load();
        $this->assign('options', $collection);

        return parent::toHtml();
    }

    protected function _initChildren()
    {
        $this->setChild('deleteButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete'),
                    'onclick'   => 'option.del(this)',
					'class' => 'delete delete-poll-answer'
                ))
        );

        $this->setChild('addButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Add New Option'),
                    'onclick'   => 'option.add(this)',
					'class' => 'add'
                ))
        );
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('deleteButton');
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('addButton');
    }
}