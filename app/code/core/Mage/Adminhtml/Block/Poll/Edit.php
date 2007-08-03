<?php
/**
 * Poll edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Poll_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'poll';

        $this->_updateButton('save', 'label', __('Save Poll'));
        $this->_updateButton('delete', 'label', __('Delete Poll'));

        if( $this->getRequest()->getParam($this->_objectId) ) {
            $pollData = Mage::getModel('poll/poll')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('poll_data', $pollData);
        }

        $this->_formScripts[] = "
            var answers = function() {
                return {
                    add : function() {
                        var req = { data : Ext.util.JSON.encode({
                                        answer_title : $('answer_title').value,
                                        poll_id : $('poll_id').value
                                        })
                                  };

                        var con = new Ext.lib.Ajax.request('POST', '" . Mage::getUrl('*/poll_answer/jsonSave') . "', {success:this.success,failure:this.failure}, req);
                    },

                    success : function(o) {
                        var o = Ext.util.JSON.decode(o.responseText);
                        if( o.error ) {
                            alert(o.message);
                        } else {
                            $('answer_title').value = '';
                            $('answer_title').focus();
                            answersGridJsObject.reload();
                        }
                    },

                    delete : function(id) {
                        if( id > 0 ) {
                            if( confirm('" . __('Are you sure you want to do this?') . "') == true ) {
                                var req = \$H({id : id});

                                var con = new Ext.lib.Ajax.request('POST', '" . Mage::getUrl('*/poll_answer/jsonDelete') . "', {success:this.success,failure:this.failure}, req);
                            }
                        }
                        return false;
                    }
                }
            }();
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('poll_data') && Mage::registry('poll_data')->getId() ) {
            return __('Edit Poll') . " '" . Mage::registry('poll_data')->getPollTitle() . "'";
        } else {
            return __('New Poll');
        }
    }
}