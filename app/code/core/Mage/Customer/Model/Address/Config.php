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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer address config
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Ivan Chepurnyi <ivan.chepurnoy@varien.com>
 */
class Mage_Customer_Model_Address_Config extends Mage_Core_Model_Config_Base
{
    const DEFAULT_ADDRESS_RENDERER = 'customer/address_renderer_default';

    protected $_types;

    public function __construct()
    {
        parent::__construct(Mage::getConfig()->getNode('global/customer/address'));
    }

    public function getFormats()
    {
        if(is_null($this->_types)) {
            $this->_types = array();
            foreach($this->getNode('formats')->children() as $typeCode=>$typeConfig) {
                $type = new Varien_Object();
                $type->setCode($typeCode)
                    ->setTitle($typeConfig->title)
                    ->setDefaultFormat($typeConfig->defaultFormat);

                $renderer = $typeConfig->renderer;
                if (!$renderer) {
                    $renderer = self::DEFAULT_ADDRESS_RENDERER;
                }

                $type->setRenderer(Mage::getModel($renderer)->setType($type));

                $this->_types[] = $type;
            }
        }

        return $this->_types;
    }

    public function getFormatByCode($typeCode)
    {
        foreach($this->getFormats() as $type) {
            if($type->getCode()==$typeCode) {
                return $type;
            }
        }
        return false;
    }
} // Class Mage_Customer_Model_Address_Config End