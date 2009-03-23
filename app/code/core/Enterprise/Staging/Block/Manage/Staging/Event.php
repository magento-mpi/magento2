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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging event block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Event extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('enterprise_staging_event');
        $this->setTemplate('enterprise/staging/manage/staging/event.phtml');
        $this->helper = Mage::helper('enterprise_staging');
    }

    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('enterprise_staging')->__('Back'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/edit', array('id'=>$this->getStaging()->getId())).'\')',
                    'class' => 'back'
                ))
        );

        $this->setChild('event_form',
            $this->getLayout()->createBlock('enterprise_staging/manage_staging_event_form')
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve staging object
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }

    /**
     * Retrieve event object
     *
     * @return Enterprise_Staging_Model_Staging_Event
     */
    public function getEvent()
    {
        if (!($this->getData('event') instanceof Enterprise_Staging_Model_Staging_Event)) {
            $this->setData('event', Mage::registry('event'));
        }
        return $this->getData('event');
    }
}