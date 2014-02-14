<?php

namespace Magento\Exception;

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ 
class StateException extends Exception
{
    const INVALID_STATE = 1;
    const EXPIRED = 3;
    const INPUT_MISMATCH = 5;
}
