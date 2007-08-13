<?php
/**
 * Adminhtml add Review main block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
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

        $this->_formScripts[] = '
            toggleParentVis("add_review_form");
        ';

        $this->_formInitScripts[] = '
            var review = function() {
                return {
                    productInfoUrl : null,
                    formHidden : true,

                    gridRowClick : function(data, click) {
                        review.productInfoUrl = click.currentTarget.id;
                        review.loadProductData();
                        review.showForm();
                        review.formHidden = false;
                    },

                    loadProductData : function() {
                        var con = new Ext.lib.Ajax.request(\'POST\', review.productInfoUrl, {success:review.reqSuccess,failure:review.reqFailure});
                    },

                    showForm : function() {
                        toggleParentVis("add_review_form");
                        toggleParentVis("add_review_grid");
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