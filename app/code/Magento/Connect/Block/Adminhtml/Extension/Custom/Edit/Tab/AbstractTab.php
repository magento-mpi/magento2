<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract for extension info tabs
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab;

abstract class AbstractTab
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * TODO
     */
    protected $_addRowButtonHtml;

    /**
     * TODO
     */
    protected $_removeRowButtonHtml;

    /**
     * TODO
     */
    protected $_addFileDepButtonHtml;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Connect\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Connect\Model\Session $session,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->setData($session->getCustomExtensionPackageFormData());
    }

    /**
     * TODO   remove ???
     */
    public function initForm()
    {
        return $this;
    }

    /**
     * TODO
     */
    public function getValue($key, $default='')
    {
        $value = $this->getData($key);
        return htmlspecialchars($value ? $value : $default);
    }

    /**
     * TODO
     */
    public function getSelected($key, $value)
    {
        return $this->getData($key)==$value ? 'selected="selected"' : '';
    }

    /**
     * TODO
     */
    public function getChecked($key)
    {
        return $this->getData($key) ? 'checked="checked"' : '';
    }

    /**
     * TODO
     */
    public function getAddRowButtonHtml($container, $template, $title='Add')
    {
        if (!isset($this->_addRowButtonHtml[$container])) {
            $this->_addRowButtonHtml[$container] = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Widget\Button')
                    ->setType('button')
                    ->setClass('add')
                    ->setLabel(__($title))
                    ->setOnClick("addRow('".$container."', '".$template."')")
                    ->toHtml();
        }
        return $this->_addRowButtonHtml[$container];
    }

    /**
     * TODO
     */
    public function getRemoveRowButtonHtml($selector='span')
    {
        if (!$this->_removeRowButtonHtml) {
            $this->_removeRowButtonHtml = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Widget\Button')
                    ->setType('button')
                    ->setClass('delete')
                    ->setLabel(__('Remove'))
                    ->setOnClick("removeRow(this, '".$selector."')")
                    ->toHtml();
        }
        return $this->_removeRowButtonHtml;
    }

    public function getAddFileDepsRowButtonHtml($selector='span', $filesClass='files')
    {
        if (!$this->_addFileDepButtonHtml) {
            $this->_addFileDepButtonHtml = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Widget\Button')
                    ->setType('button')
                    ->setClass('add')
                    ->setLabel(__('Add files'))
                    ->setOnClick("showHideFiles(this, '".$selector."', '".$filesClass."')")
                    ->toHtml();
        }
        return $this->_addFileDepButtonHtml;

    }

    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return '';
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return '';
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
