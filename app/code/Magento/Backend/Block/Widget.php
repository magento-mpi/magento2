<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base widget class
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
namespace Magento\Backend\Block;

class Widget extends \Magento\Backend\Block\Template
{
    public function getId()
    {
        if (null === $this->getData('id')) {
            $this->setData('id', $this->helper('Magento\Core\Helper\Data')->uniqHash('id_'));
        }
        return $this->getData('id');
    }

    /**
     * Get HTML ID with specified suffix
     *
     * @param string $suffix
     * @return string
     */
    public function getSuffixId($suffix)
    {
        return "{$this->getId()}_{$suffix}";
    }

    public function getHtmlId()
    {
        return $this->getId();
    }

    /**
     * Get current url
     *
     * @param array $params url parameters
     * @return string current url
     */
    public function getCurrentUrl($params = array())
    {
        if (!isset($params['_current'])) {
            $params['_current'] = true;
        }
        return $this->getUrl('*/*/*', $params);
    }

    protected function _addBreadcrumb($label, $title=null, $link=null)
    {
        $this->getLayout()->getBlock('breadcrumbs')->addLink($label, $title, $link);
    }

    /**
     * Create button and return its html
     *
     * @param string $label
     * @param string $onclick
     * @param string $class
     * @param string $buttonId
     * @param array $dataAttr
     * @return string
     */
    public function getButtonHtml($label, $onclick, $class = '', $buttonId = null, $dataAttr = array())
    {
        return $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'label'     => $label,
                'onclick'   => $onclick,
                'class'     => $class,
                'type'      => 'button',
                'id'        => $buttonId,
            ))
            ->setDataAttribute($dataAttr)
            ->toHtml();
    }

    public function getGlobalIcon()
    {
        return '<img src="' . $this->getViewFileUrl('images/fam_link.gif')
            . '" alt="' . __('Global Attribute')
            . '" title="' . __('This attribute shares the same value in all stores.')
            . '" class="attribute-global"/>';
    }
}

