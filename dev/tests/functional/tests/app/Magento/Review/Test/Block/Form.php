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

namespace Magento\Review\Test\Block;

use Mtf\Block\Form as BlockForm;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Review form
 *
 * @package Magento\Review\Test\Block
 */
class Form extends BlockForm
{
    /**
     * 'Submit' review button selector
     *
     * @var string
     */
    protected $submitButtonSelector = '.action.submit';

    /**
     * Nickname selector
     *
     * @var string
     */
    protected $nicknameSelector = '#nickname_field';

    /**
     * Title selector
     *
     * @var string
     */
    protected $titleSelector = '#summary_field';

    /**
     * Detail selector
     *
     * @var string
     */
    protected $detailSelector = '#review_field';

    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        $this->_mapping = array(
            'nickname' => $this->nicknameSelector,
            'title' => $this->titleSelector,
            'detail' => $this->detailSelector,
        );
    }

    /**
     * Submit review form
     */
    public function submit()
    {
        $this->_rootElement->find($this->submitButtonSelector, Locator::SELECTOR_CSS)->click();
    }
}
