<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Category\Attribute\Source;

/**
 * Catalog category landing page attribute source
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Layout
    extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var array
     */
    protected $_cmsLayouts;

    /**
     * @param array $cmsLayouts
     */
    public function __construct(array $cmsLayouts = array())
    {
        $this->_cmsLayouts = $cmsLayouts;
    }

    /**
     * Return cms layout update options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            foreach ($this->_cmsLayouts as $layoutName => $layoutConfig) {
                $this->_options[] = array(
                   'value' => $layoutName,
                   'label' => $layoutConfig
                );
            }
            array_unshift($this->_options, array('value'=>'', 'label' => __('No layout updates')));
        }
        return $this->_options;
    }
}
