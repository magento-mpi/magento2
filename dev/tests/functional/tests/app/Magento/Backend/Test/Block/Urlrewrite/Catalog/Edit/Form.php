<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Urlrewrite\Catalog\Edit;

use Magento\Backend\Test\Block\Widget\Form as FormWidget;

/**
 * Class Form
 * Catalog URL rewrite edit form
 *
 * @package Magento\Backend\Test\Block\Urlrewrite\Catalog\Edit
 */
class Form extends FormWidget
{
    /**
     * Mapping for field locator
     *
     * @var array
     */
    protected $_mapping = array(
        'type' => '[id="is_system"]',
        'id_path' => '[id="id_path"]',
        'store_id' => '[id="store_id"]',
        'request_path' => '[id="request_path"]',
        'target_path' => '[id="target_path"]',
        'redirect' => array(
            'selector' => '[id="options"]',
            'input' => 'select',
        ),
        'description' => '[id="description"]',
    );
}
