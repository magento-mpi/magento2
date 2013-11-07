<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Review;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid as GridAbstract;

/**
 * Reviews grid
 *
 * @package Magento\Backend\Test\Block\Review
 */
class Grid extends GridAbstract
{
    /**
     * Id selector
     *
     * @var string
     */
    protected $idSelector = '#reviwGrid_filter_review_id';

    /**
     * Title selector
     *
     * @var string
     */
    protected $titleSelector = '#reviwGrid_filter_title';

    /**
     * Status selector
     *
     * @var string
     */
    protected $statusSelector = '#reviwGrid_filter_status';

    /**
     * {@inheritdoc}
     */
    protected $editLink = '//td[contains(@class,"col-action")]//a';

    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'id' => array(
                'selector' => $this->idSelector,
            ),
            'title' => array(
                'selector' => $this->titleSelector,
            ),
            'status' => array(
                'selector' => $this->statusSelector,
                'input' => 'select',
            ),
        );
    }
}
