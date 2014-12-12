<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

class HeredocTerminatingLineCondition extends LineBreakCondition
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct(';');
    }

    /**
     * {@inheritdoc}
     */
    public function process(&$tokens, $nextToken)
    {
        $token = $nextToken;
        // since condition is a string, only need to check if incoming token is a string
        if (is_string($nextToken)) {
            // if the next token starts with condition, then get rid of last token and accept this
            if (strncmp($nextToken, $this->condition, strlen($this->condition)) === 0) {
                array_pop($tokens);
            }
        } elseif ($nextToken instanceof CallLineBreak) {
            // drop the next token from being added
            $token = null;
        }

        return $token;
    }
}
