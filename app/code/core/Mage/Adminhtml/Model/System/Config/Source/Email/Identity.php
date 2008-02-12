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


class Mage_Adminhtml_Model_System_Config_Source_Email_Identity
{
    protected $_options = null;

    const XML_IDENT_PATH = 'trans_email';

    public function toOptionArray()
    {
        if (is_null($this->_options)) {
            $this->_options = array();
            $config = Mage::getStoreConfig(self::XML_IDENT_PATH);

            foreach ($config as $key => $node) {
                $label      = $node['name'];
                $this->_options[] = array(
                    'value' => preg_replace('#^ident_(.*)$#', '$1', $key),
                    'label' => Mage::helper('adminhtml')->__($label)
                );
            }
        }
        return $this->_options;
    }
}