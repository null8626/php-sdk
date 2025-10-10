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

namespace DBL;

use DBL\DBL;

/**
 * Top.gg API v1 client
 */
class V1DBL extends DBL
{
  /**
   * Updates the application commands list in your Discord bot's Top.gg page.
   *
   * @param   array $commands A list of application commands in raw Discord API JSON objects. This cannot be empty.
   */
  public function post_commands(array $commands)
  {
    $this->api->req("POST", "/v1/projects/@me/commands", $commands);
  }

  /**
   * Gets the latest vote information of a Top.gg user on your project.
   *
   * @param   int         $id     The user's ID.
   * @param   string      $source The ID type to use. Defaults to "discord".
   * @return  array|null
   */
  public function get_vote(int $id, string $source = "discord"): array|null
  {
    if ($source !== "topgg" && $source !== "discord")
    {
      throw new \Exception("source argument must be \"topgg\" or \"discord\".");
    }

    $result = $this->api->req("GET", "/v1/projects/@me/votes/{$id}", ["source" => $source]);

    if ($result["status"] === "404") {
      return null;
    }

    return $result["json"];
  }
}

?>
