<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml add product review form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Review_Add_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $statuses = Mage::getModel('review/review')
            ->getStatusCollection()
            ->load()
            ->toOptionArray();

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('add_review_form', array('legend' => __('Review Details')));

        $fieldset->addField('product_name', 'note', array(
                                'label'     => __('Product'),
                                'text'      => 'product_name',
                            )
        );

        $fieldset->addField('detailed_rating', 'note', array(
                                'label'     => __('Product Rating'),
                                'required'  => true,
                                'text'      => $this->getLayout()->createBlock('adminhtml/review_rating_detailed')->toHtml(),
                            )
        );

        $fieldset->addField('status_id', 'select', array(
                                'label'     => __('Status'),
                                'required'  => true,
                                'name'      => 'status_id',
                                'values'    => $statuses,
                            )
        );

        $fieldset->addField('nickname', 'text', array(
            'name'      => 'nickname',
            'title'     => __('Nickname'),
            'label'     => __('Nickname'),
            'maxlength' => '50',
            'required'  => true,
        ));

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'title'     => __('Summary of Review'),
            'label'     => __('Summary of Review'),
            'maxlength' => '255',
            'required'  => true,
        ));

        $fieldset->addField('detail', 'textarea', array(
            'name'      => 'detail',
            'title'     => __('Review'),
            'label'     => __('Review'),
            'style' => 'width: 98%; height: 600px;',
            'required'  => true,
        ));

        $fieldset->addField('product_id', 'hidden', array(
            'name'      => 'product_id',
        ));

        $gridFieldset = $form->addFieldset('add_review_grid', array('legend' => __('Please select a product')));
        $gridFieldset->addField('products_grid', 'note', array(
            'text' => $this->getLayout()->createBlock('adminhtml/review_product_grid')->toHtml(),
        ));


        $form->setMethod('POST');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction(Mage::getUrl('*/*/post'));

        $this->setForm($form);
    }
}