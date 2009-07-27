<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Enc
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Store_Switcher extends Mage_Adminhtml_Block_Store_Switcher
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('enterprise/cms/store/switcher.phtml');
    }

    /**
     * Prepares form object and return it's html code
     *
     * @return string
     */
    public function getFormHtml()
    {
        if ($this->getRepostData()) {
            $form = new Varien_Data_Form();
            foreach ($this->getRepostData() as $key => $value) {
                $form->addField($key, 'hidden', array('name' => $key));
            }
            $form->setValues($this->getRepostData());
            return $form->toHtml();
        }
        return '';
    }

    /**
     * Generates action url for form
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        if (!$this->hasData('form_action_url')) {
            $this->setData('form_action_url', $this->getUrl('*/*/*', array('_current' => true)));
        }
        return $this->getData('form_action_url');
    }

    /**
     * Retrieve id of currently selected store
     *
     * @return int
     */
    public function getStoreId()
    {
        if (!$this->hasStoreId()) {
            $this->setData('store_id', (int)$this->getRequest()->getPost('store_switcher'));
        }
        return $this->getData('store_id');
    }
}
