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

enum WidgetType: string {
  case DiscordBot = "discord/bot";
  case DiscordServer = "discord/server";
}

final class Widget {
  private const BASE_URL = "https://top.gg/api/v1";

  /**
   * Generates a large widget URL.
   *
   * @param   WidgetType   $ty       The widget type.
   * @param   int          $id       The ID.
   * @return  string
   */
  public static function large(WidgetType $ty, int $id): string {
    return self::BASE_URL . "/widgets/large/" . $ty->value . "/$id";
  }

  /**
   * Generates a small widget URL for displaying votes.
   *
   * @param   WidgetType   $ty       The widget type.
   * @param   int          $id       The ID.
   * @return  string
   */
  public static function votes(WidgetType $ty, int $id): string {
    return self::BASE_URL . "/widgets/small/votes/" . $ty->value . "/$id";
  }

  /**
   * Generates a small widget URL for displaying a project's owner.
   *
   * @param   WidgetType   $ty       The widget type.
   * @param   int          $id       The ID.
   * @return  string
   */
  public static function owner(WidgetType $ty, int $id): string {
    return self::BASE_URL . "/widgets/small/owner/" . $ty->value . "/$id";
  }

  /**
   * Generates a small widget URL for displaying social stats.
   *
   * @param   WidgetType   $ty       The widget type.
   * @param   int          $id       The ID.
   * @return  string
   */
  public static function social(WidgetType $ty, int $id): string {
    return self::BASE_URL . "/widgets/small/social/" . $ty->value . "/$id";
  }
}

?>