<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Framework\Filter;

/**
 * Magento filter factory
 */
class Factory extends AbstractFactory
{
    /**
     * Set of filters
     *
     * @var array
     */
    protected $invokableClasses = array(
        'email' => 'Magento\Framework\Filter\Email',
        'money' => 'Magento\Framework\Filter\Money',
        'simple' => 'Magento\Framework\Filter\Template\Simple',
        'object' => 'Magento\Framework\Filter\Object',
        'sprintf' => 'Magento\Framework\Filter\Sprintf',
        'template' => 'Magento\Framework\Filter\Template',
        'arrayFilter' => 'Magento\Framework\Filter\ArrayFilter',
        'removeAccents' => 'Magento\Framework\Filter\RemoveAccents',
        'splitWords' => 'Magento\Framework\Filter\SplitWords',
        'removeTags' => 'Magento\Framework\Filter\RemoveTags',
        'stripTags' => 'Magento\Framework\Filter\StripTags',
        'truncate' => 'Magento\Framework\Filter\Truncate',
        'encrypt' => 'Magento\Framework\Filter\Encrypt',
        'decrypt' => 'Magento\Framework\Filter\Decrypt',
        'translit' => 'Magento\Framework\Filter\Translit',
        'translitUrl' => 'Magento\Framework\Filter\TranslitUrl'
    );

    /**
     * Shared instances, by default is shared
     *
     * @var array
     */
    protected $shared = array(
        'Magento\Framework\Filter\Sprintf' => false,
        'Magento\Framework\Filter\Money' => false,
        'Magento\Framework\Filter\RemoveAccents' => false,
        'Magento\Framework\Filter\SplitWords' => false,
        'Magento\Framework\Filter\StripTags' => false,
        'Magento\Framework\Filter\Truncate' => false
    );
}
