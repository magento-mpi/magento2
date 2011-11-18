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
class Mage_Core_Block_Text_Tag_Css extends Mage_Core_Block_Text_Tag
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTagName('link');
        $this->setTagParams(array('rel'=>'stylesheet', 'type'=>'text/css', 'media'=>'all'));
    }

    function setHref($href, $type=null)
    {
        $type = (string)$type;
        if (empty($type)) {
            $type = 'skin';
        }
        $url = Mage::getBaseUrl($type).$href;

        return $this->setTagParam('href', $url);
    }

}
