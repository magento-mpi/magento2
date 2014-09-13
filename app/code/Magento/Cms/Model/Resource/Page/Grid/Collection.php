<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource\Page\Grid;

use Magento\Cms\Model\Resource\Page\Collection as PageCollection;

/**
 * CMS page collection
 */
class Collection extends PageCollection
{
    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag = true;
}
