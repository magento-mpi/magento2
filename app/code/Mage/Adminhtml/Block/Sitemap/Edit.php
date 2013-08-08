<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sitemap edit form container
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sitemap_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Init container
     */
    protected function _construct()
    {
        $this->_objectId = 'sitemap_id';
        $this->_controller = 'sitemap';

        parent::_construct();

        $this->_addButton('generate', array(
            'label'   => __('Save & Generate'),
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array(
                        'event' => 'save',
                        'target' => '#edit_form',
                        'eventData' => array(
                            'action' => array('args' => array(
                                'generate' => '1'
                            )),
                        ),
                    ),
                ),
            ),
            'class'   => 'add',
        ));
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('sitemap_sitemap')->getId()) {
            return __('Edit Sitemap');
        }
        else {
            return __('New Sitemap');
        }
    }
}
