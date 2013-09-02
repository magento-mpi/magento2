<?php
/**
 * Status column for Cache grid
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Block_Cache_Grid_Column_Statuses extends Magento_Backend_Block_Widget_Grid_Column
{
    /**
     * @var Magento_Core_Model_Cache_TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Cache_TypeListInterface $cacheTypeList
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Cache_TypeListInterface $cacheTypeList,
        array $data = array()
    ) {
        parent::__construct ($context, $coreStoreConfig, $data);
        $this->_cacheTypeList = $cacheTypeList;
    }

    /**
     * Add to column decorated status
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return array($this, 'decorateStatus');
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param  Magento_Core_Model_Abstract $row
     * @param Magento_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $invalidedTypes = $this->_cacheTypeList->getInvalidated();
        if (isset($invalidedTypes[$row->getId()])) {
            $cell = '<span class="grid-severity-minor"><span>' . __('Invalidated') . '</span></span>';
        } else {
            if ($row->getStatus()) {
                $cell = '<span class="grid-severity-notice"><span>' . $value . '</span></span>';
            } else {
                $cell = '<span class="grid-severity-critical"><span>' . $value . '</span></span>';
            }
        }
        return $cell;
    }
}
