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
 * Adminhtml add urlrewrite main block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author	   Kyaw Soe Lynn Maung <vincent@varien.com>
 */

class Mage_Adminhtml_Block_Urlrewrite_Add extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_controller = 'urlrewrite';
        $this->_mode = 'add';

        $this->_updateButton('save', 'label', Mage::helper('urlrewrite')->__('Save Url'));
        $this->_updateButton('save', 'id', 'save_button');

        $this->_updateButton('reset', 'id', 'reset_button');

        $this->_formScripts[] = '
            toggleParentVis("add_urlrewrite_form");
            toggleParentVis("add_urlrewrite_grid");
            toggleParentVis("add_urlrewrite_category");
            toggleVis("save_button");
            toggleVis("reset_button");
        ';

        $this->_formInitScripts[] = '
            var urlrewrite = function() {
                return {
                    productInfoUrl : null,
                    formHidden : true,
					cateoryInfoUrl : null,
                    gridRowClick : function(data, click) {

                        if(Event.findElement(click,\'TR\').id){
                            urlrewrite.productInfoUrl = Event.findElement(click,\'TR\').id;
                            urlrewrite.loadProductData();
                            urlrewrite.showForm();
                            urlrewrite.formHidden = false;
                        }
                    },

                    loadProductData : function() {

                    	urlrewrite.categoryInfoUrl = urlrewrite.productInfoUrl.replace("jsonProductInfo","getCategoryInfo");
                        var con = new Ext.lib.Ajax.request(\'POST\', urlrewrite.productInfoUrl, {success:urlrewrite.reqSuccess,failure:urlrewrite.reqFailure});
                    },

                    showForm : function() {
                        //toggleParentVis("add_urlrewrite_form");
                        toggleParentVis("add_urlrewrite_grid");
                        toggleParentVis("add_urlrewrite_category");
                        toggleVis("save_button");
                        toggleVis("reset_button");
                    },

                    showForm1 : function() {
                        toggleParentVis("add_urlrewrite_form");
                        //toggleParentVis("add_urlrewrite_grid");
                        toggleParentVis("add_urlrewrite_category");
                        toggleVis("save_button");
                        toggleVis("reset_button");
                    },
                    updateRating: function() {
                    	/*
                        elements = [$("select_stores"), $("rating_detail").getElementsBySelector("input[type=\'radio\']")].flatten();
                         $(\'save_button\').disabled = true;
                        new Ajax.Updater("rating_detail", "'.$this->getUrl('*/*/ratingItems').'", {parameters:Form.serializeElements(elements), evalScripts: true,  onComplete:function(){ $(\'save_button\').disabled = false; } });
                        */
                        var typeDom = $("type");
                        // 2 : product
                        if (typeDom.options[typeDom.options.selectedIndex].value == 2) {
                        	toggleParentVis("add_urlrewrite_grid");
                        	toggleParentVis("add_urlrewrite_type");
                        	toggleVis("save_button");
                        	toggleVis("reset_button");
                        } else if (typeDom.options[typeDom.options.selectedIndex].value == 1) {
                        	toggleParentVis("add_urlrewrite_category");
                        	toggleParentVis("add_urlrewrite_type");
                        	toggleVis("save_button");
                        	toggleVis("reset_button");
                        }
                    },

                    reqSuccess :function(o) {
                        var response = Ext.util.JSON.decode(o.responseText);
                        if( response.error ) {
                            alert(response.message);
                        } else if( response.id ){
                            $("product_id").value = response.id;

                            $("product_name").innerHTML = \'<a href="' . Mage::getUrl('*/catalog_product/edit') . 'id/\' + response.id + \'" target="_blank">\' + response.name + \'</a>\';
                            $("id_path").value = "product/" + response.id;
                            $("request_path").value = response.url_key + ".html";
                            $("target_path").value = "catalog/product/view/id/" + response.id;
                            var con = new Ext.lib.Ajax.request(\'POST\', urlrewrite.categoryInfoUrl, {success:urlrewrite.loadCategory,failure:urlrewrite.reqFailure});
                        } else if( response.message ) {
                            alert(response.message);
                        }
                    },

                    loadCategory: function(o) {
        				if (! o.responseText ) {
        					alert(o.message);
        				} else {
        					var response = Ext.util.JSON.decode(o.responseText);
        					buildCategoryTree(_root, response);
        					_tree.expandAll();
        					_tree.disableChecked();
        					//$("category_tree").innerHTML = o.responseText;
        				}
                    }
                }
            }();

             Event.observe(window, \'load\', function(){
                 Event.observe($("type"), \'change\', urlrewrite.updateRating);
           });
        ';
    }

    public function getHeaderText()
    {
        return Mage::helper('urlrewrite')->__('Add New Urlrewrite');
    }
}