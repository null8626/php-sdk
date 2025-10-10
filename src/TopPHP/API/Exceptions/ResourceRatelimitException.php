<?php

/**
 * This file is respectively apart of the TopPHP project.
 *
 * Copyright (c) 2021-present James Walston
 * Some rights are reserved.
 *
 * This copyright is subject to the MIT license which
 * fully entails the details in the LICENSE file.
 */

namespace DBL\API\Exceptions;

/**
 * The Resource-specific Ratelimits Exception class.
 * This allows for exceptions to be made when
 * the HTTP hits a rate limit for a specific request.
 */
class ResourceRatelimitException extends \Exception
{
  /** @var mixed */
  public $message;

  /**
   * Creates a ResourceRatelimitException class.
   *
   * @param   string      $message  The error message.
   */
  public function __construct(string $message)
  {
    parent::__construct($message);

    $this->message = $message;
  }
}

?>
