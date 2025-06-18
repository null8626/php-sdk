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

namespace DBL\Structs;

interface BaseStruct
{
  public function __construct(array $parameters);

  /**
   * GET requests are shown respectively here.
   * These use customized formatting for different
   * procedures.
   */
  /** Show statistics for all bots/users or specified. */
  public function get_bots();
  public function get_bot(int $id);

  /** Get information on the votes, vote check; and stats. */
  public function get_votes(int $id, int $page);
  public function get_user_vote(int $user);
  public function get_stats();

  /**
   * POST requests will be handled here, and have to be
   * taken into account differently due to their nature.
   */
  public function post_stats(array $json);

  /** Accessor methods for private instances. */
  public function getHttp();
  public function getPort();
  public function getContents();
  public function getCache();
  public function getResponse();
}

?>
