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
 * Block for authors
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

class Authors extends \Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab\AbstractTab
{
    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Connect\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Connect\Model\Session $session,
        \Magento\Json\EncoderInterface $jsonEncoder,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        parent::__construct($context, $registry, $formFactory, $session, $data);
    }

    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Authors');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Authors');
    }

    /**
     * Return add author button html
     *
     * @return string
     */
    public function getAddAuthorButtonHtml()
    {
        return $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setType(
            'button'
        )->setClass(
            'add'
        )->setLabel(
            __('Add Author')
        )->setOnClick(
            'addAuthor()'
        )->toHtml();
    }

    /**
     * Return array of authors
     *
     * @return string[]
     */
    public function getAuthors()
    {
        $authors = array();
        if ($this->getData('authors')) {
            $temp = array();
            foreach ($this->getData('authors') as $param => $values) {
                if (is_array($values)) {
                    foreach ($values as $key => $value) {
                        $temp[$key][$param] = $value;
                    }
                }
            }
            foreach ($temp as $key => $value) {
                $authors[$key] = $this->_jsonEncoder->encode($value);
            }
        }
        return $authors;
    }
}
