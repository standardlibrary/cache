<?php
declare(strict_types = 1);
/**
 * This file is part of the Standard Library Cache package.
 * For the full copyright information please view the LICENCE file that was
 * distributed with this package.
 *
 * @copyright Simon Deeley 2017
 */

namespace StandardLibrary\Exceptions\Cache;

use InvalidArgumentException as BaseException;
use Psr\Cache\InvalidArgumentException as PsrException;

/**
 * Standard Library cache Exception
 *
 */
final class InvalidArgumentException extends BaseException implements PsrException
{
    // ...
}
