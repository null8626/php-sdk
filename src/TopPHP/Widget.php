<?php

namespace DBL;

final class Widget {
  private const BASE_URL = "https://top.gg/api/v1";

  /**
   * Generates a large widget URL.
   *
   * @param   int     $id       The ID.
   * @return  string
   */
  public static function large(int $id): string {
    return self::BASE_URL . "/widgets/large/$id";
  }

  /**
   * Generates a small widget URL for displaying votes.
   *
   * @param   int     $id       The ID.
   * @return  string
   */
  public static function votes(int $id): string {
    return self::BASE_URL . "/widgets/small/votes/$id";
  }

  /**
   * Generates a small widget URL for displaying an entity's owner.
   *
   * @param   int     $id       The ID.
   * @return  string
   */
  public static function owner(int $id): string {
    return self::BASE_URL . "/widgets/small/owner/$id";
  }

  /**
   * Generates a small widget URL for displaying social stats.
   *
   * @param   int     $id       The ID.
   * @return  string
   */
  public static function social(int $id): string {
    return self::BASE_URL . "/widgets/small/social/$id";
  }
}

?>