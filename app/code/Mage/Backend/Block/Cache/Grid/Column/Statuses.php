<?php
/**
 * Status column for Cache grid
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Block_Cache_Grid_Column_Statuses extends Mage_Backend_Block_Widget_Grid_Column
{
    /**
     * Invalidated cache types
     *
     * @var array
     */
    protected $_invalidedTypes = array();

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $app
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $app,
        array $data = array()
    ) {
        parent::__construct ($context, $data);

        $this->_app = $app;
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
        $this->_invalidatedTypes = $this->_app->getCacheInstance()->getInvalidatedTypes();
        if (isset($this->_invalidatedTypes[$row->getId()])) {
            $cell = '<span class="grid-severity-minor"><span>' . $this->__('Invalidated') . '</span></span>';
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
