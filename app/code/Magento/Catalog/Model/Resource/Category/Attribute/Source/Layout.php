<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog category landing page attribute source
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Category\Attribute\Source;

class Layout
    extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param \Magento\Core\Model\Config $coreConfig
     */
    public function __construct(
        \Magento\Core\Model\Config $coreConfig
    ) {
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Return cms layout update options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $layouts = array();
            foreach ($this->_coreConfig->getNode('global/cms/layouts')->children() as $layoutName=>$layoutConfig) {
                $this->_options[] = array(
                   'value'=>$layoutName,
                   'label'=>(string)$layoutConfig->label
                );
            }
            array_unshift($this->_options, array('value'=>'', 'label' => __('No layout updates')));
        }
        return $this->_options;
    }
}
