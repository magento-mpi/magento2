<?php
/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block\Product;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Client\Element;

/**
 * Reviews frontend block
 *
 * @package Magento\Review\Test\Block
 */
class View extends Block
{
    /**
     * Review form selector
     *
     * @var string
     */
    protected $formSelector = '#review-form';

    /**
     * Review item selector
     *
     * @var string
     */
    protected $itemSelector = '.reviews.items .item.review';

    /**
     * Legend selector
     *
     * @var string
     */
    protected $legendSelector = 'legend';

    /**
     * Nickname selector
     *
     * @var string
     */
    protected $nicknameSelector = '.nickname';

    /**
     * Title selector
     *
     * @var string
     */
    protected $titleSelector = '.title';

    /**
     * Detail selector
     *
     * @var string
     */
    protected $detailSelector = '.content';

    /**
     * Selectors mapping
     *
     * @var array
     */
    protected $selectorsMapping;

    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        parent::_init();
        $this->selectorsMapping = array(
            'nickname' => $this->nicknameSelector,
            'title' => $this->titleSelector,
            'detail' => $this->detailSelector,
        );
    }

    /**
     * Get review form
     *
     * @return \Magento\Review\Test\Block\Form
     */
    public function getReviewForm()
    {
        return Factory::getBlockFactory()->getMagentoReviewForm(
            $this->_rootElement->find($this->formSelector, Locator::SELECTOR_ID)
        );
    }

    /**
     * Get legend
     *
     * @return Element
     */
    public function getLegend()
    {
        return $this->_rootElement->find($this->legendSelector);
    }
    /**
     * Get first review item
     *
     * @return Element
     */
    public function getFirstReview()
    {
        return $this->_rootElement->find($this->itemSelector);
    }

    /**
     * Get selector field for review on product view page
     *
     * @param string $field
     * @return string
     * @throws \Exception
     */
    public function getFieldSelector($field)
    {
        if (!isset($this->selectorsMapping[$field])) {
            throw new \Exception(sprintf('Selector of field "%s" is not defined', $field));
        }
        return $this->selectorsMapping[$field];
    }
}
