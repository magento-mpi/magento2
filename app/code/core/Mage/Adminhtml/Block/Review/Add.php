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
 * Adminhtml add Review main block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Review_Add extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_controller = 'review';
        $this->_mode = 'add';

        $this->_updateButton('save', 'label', __('Save Review'));
        $this->_updateButton('save', 'id', 'save_button');

        $this->_updateButton('reset', 'id', 'reset_button');

        $this->_formScripts[] = '
            toggleParentVis("add_review_form");
            toggleVis("save_button");
            toggleVis("reset_button");
        ';

        $this->_formInitScripts[] = '
            var review = function() {
                return {
                    productInfoUrl : null,
                    formHidden : true,

                    gridRowClick : function(data, click) {
                        if(click.currentTarget.id){
                            review.productInfoUrl = click.currentTarget.id;
                            review.loadProductData();
                            review.showForm();
                            review.formHidden = false;
                        }
                    },

                    loadProductData : function() {
                        var con = new Ext.lib.Ajax.request(\'POST\', review.productInfoUrl, {success:review.reqSuccess,failure:review.reqFailure});
                    },

                    showForm : function() {
                        toggleParentVis("add_review_form");
                        toggleParentVis("add_review_grid");
                        toggleVis("save_button");
                        toggleVis("reset_button");
                    },

                    reqSuccess :function(o) {
                        var response = Ext.util.JSON.decode(o.responseText);
                        if( response.error ) {
                            alert(response.message);
                        } else if( response.id ){
                            $("product_id").value = response.id;
                            console.log(response);
                            $("product_name").innerHTML = \'<a href="' . Mage::getUrl('*/catalog_product/edit') . 'id/\' + response.id + \'" target="_blank">\' + response.name + \'</a>\';
                        } else if( response.message ) {
                            alert(response.message);
                        }
                    }
                }
            }();
        ';
    }

    public function getHeaderText()
    {
        return __('Add New Review');
    }
}