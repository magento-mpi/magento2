<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid;

/**
 * Backend grid container block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Container extends \Magento\Backend\Block\Widget\Container
{
    /**#@+
     * Initialization parameters in pseudo-constructor
     */
    const PARAM_BLOCK_GROUP = 'block_group';

    const PARAM_BUTTON_NEW = 'button_new';

    const PARAM_BUTTON_BACK = 'button_back';

    /**#@-*/

    /**
     * @var string
     */
    protected $_addButtonLabel;

    /**
     * @var string
     */
    protected $_backButtonLabel;

    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_Backend';

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/grid/container.phtml';

    /**
     * Initialize object state with incoming parameters
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        if ($this->hasData(self::PARAM_BLOCK_GROUP)) {
            $this->_blockGroup = $this->_getData(self::PARAM_BLOCK_GROUP);
        }
        if ($this->hasData(self::PARAM_BUTTON_NEW)) {
            $this->_addButtonLabel = $this->_getData(self::PARAM_BUTTON_NEW);
        } else {
            // legacy logic to support all descendants
            if (is_null($this->_addButtonLabel)) {
                $this->_addButtonLabel = __('Add New');
            }
            $this->_addNewButton();
        }
        if ($this->hasData(self::PARAM_BUTTON_BACK)) {
            $this->_backButtonLabel = $this->_getData(self::PARAM_BUTTON_BACK);
        } else {
            // legacy logic
            if (is_null($this->_backButtonLabel)) {
                $this->_backButtonLabel = __('Back');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        // check if grid was created through the layout
        if (false === $this->getChildBlock('grid')) {
            $this->setChild(
                'grid',
                $this->getLayout()->createBlock(
                    str_replace(
                        '_',
                        \Magento\Autoload\IncludePath::NS_SEPARATOR,
                        $this->_blockGroup
                    ) . '\\Block\\' . str_replace(
                        ' ',
                        '\\',
                        ucwords(str_replace('_', ' ', $this->_controller))
                    ) . '\\Grid',
                    $this->_controller . '.grid'
                )->setSaveParametersInSession(
                    true
                )
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * @return string
     */
    public function getAddButtonLabel()
    {
        return $this->_addButtonLabel;
    }

    /**
     * @return string
     */
    public function getBackButtonLabel()
    {
        return $this->_backButtonLabel;
    }

    /**
     * Create "New" button
     *
     * @return void
     */
    protected function _addNewButton()
    {
        $this->_addButton(
            'add',
            array(
                'label' => $this->getAddButtonLabel(),
                'onclick' => 'setLocation(\'' . $this->getCreateUrl() . '\')',
                'class' => 'add primary'
            )
        );
    }

    /**
     * @return void
     */
    protected function _addBackButton()
    {
        $this->_addButton(
            'back',
            array(
                'label' => $this->getBackButtonLabel(),
                'onclick' => 'setLocation(\'' . $this->getBackUrl() . '\')',
                'class' => 'back'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderCssClass()
    {
        return 'icon-head ' . parent::getHeaderCssClass();
    }

    /**
     * @return string
     */
    public function getHeaderWidth()
    {
        return 'width:50%;';
    }
}
