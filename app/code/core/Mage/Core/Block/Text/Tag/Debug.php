<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Base html block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Text_Tag_Debug extends Mage_Core_Block_Text_Tag
{

    protected function _construct()
    {
        parent::_construct();
        $this->setAttribute(array(
          'tagName'=>'xmp',
        ));
    }

    function setValue($value)
    {
        return $this->setContents(print_r($value, 1));
    }

}
