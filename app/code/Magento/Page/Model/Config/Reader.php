<?php
/**
 * Page layout config reader
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Page\Model\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/page_layouts/layouts/layout' => 'id',
    );
}
