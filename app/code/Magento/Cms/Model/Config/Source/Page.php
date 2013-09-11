<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Config\Source;

class Page implements \Magento\Core\Model\Option\ArrayInterface
{

    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = \Mage::getResourceModel('Magento\Cms\Model\Resource\Page\Collection')
                ->load()->toOptionIdArray();
        }
        return $this->_options;
    }

}
