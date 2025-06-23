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
  public function get_bots(int $limit, int $offset, string $sort_by);
  public function get_bot(int $id);

  /** Get information on your voters, vote check, weekend multiplier, and stats. */
  public function get_voters(int $page);
  public function has_voted(int $user);
  public function is_weekend();
  public function get_server_count();

  /**
   * POST requests will be handled here, and have to be
   * taken into account differently due to their nature.
   */
  public function post_server_count(int $server_count);

  /** Accessor methods for private instances. */
  public function getHttp();
  public function getPort();
  public function getContents();
  public function getCache();
  public function getResponse();
}

?>
