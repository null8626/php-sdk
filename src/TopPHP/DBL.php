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

use DBL\API\Http;
use DBL\API\Request;
use DBL\API\Exceptions\MissingTokenException;
use DBL\API\Exceptions\MissingStatsException;
use DBL\Structs\BaseStruct;
use DBL\Webhook;
use DBL\Widget;

/**
 * Represents the TopPHP/Top.gg base class.
 * This class handles all of the specified
 * GET and POST requests that the API allows
 * to be called on, and has methods declared
 * for each specific/particular usage.
 */
final class DBL implements BaseStruct
{
  /**
   * @var     string
   * @access  private
   */
  protected $http;

  /**
   * @var     int
   * @access  private
   */
  private $port;

  /**
   * @var     string
   * @access  protected
   */
  protected $token;

  /**
   * @var     mixed
   * @see     \DBL\API\Request
   * @access  public
   */
  public $api;

  /**
   * @var     array
   * @access  private
   */
  private $features;

  /**
   * @var     int
   * @access  private
   */
  private $id;

  /**
   * Creates a DBL instance.
   *
   * @param   array $parameters The parameters necessary for an established connection.
   * @return  void
   */
  public function __construct(array $parameters)
  {
    /**
     * There are 4 acceptable parameters:
     * - [string] token: The API token key.
     * - [array] [opt] auto_stats: If you want to automatically post stats.
     * - [bool] [opt] safety: For webserver protection and ensuring extra locks.
     * - [array] [opt] webhook: If you would like to send your information as a webhook.
     *
     * This will look for if any are present and valid.
     * Also, make sure a token is present. How the fuck are you gonna use it otherwise?
     * KEKW.
     */

    /**
     * Begin scanning through all of the possible accepted parameters.
     * PHP Warnings are thrown in the event that they're found to be as invalid keys
     * that are not assigned.
     */
    $this->features =
    [
      "auto_stats"  => [false],
      "safety"      => false,
      "webhook"     => []
    ];

    if (array_key_exists("auto_stats", $parameters) && $parameters["auto_stats"]) $this->features["auto_stats"][0] = true;
    if (array_key_exists("safety", $parameters) && $parameters["safety"]) $this->features["safety"] = true;
    if (array_key_exists("webhook", $parameters) && $parameters["webhook"]) $this->features["webhook"] = true;
    if (array_key_exists("token", $parameters) && $parameters["token"]) $this->token = $parameters["token"];
    else throw new MissingTokenException();

    $this->http = (!array_key_exists("webhook", $parameters) || !array_key_exists("url", $parameters["webhook"]) || !$parameters["webhook"]["url"]) ? Request::SERVER_ADDR : $parameters["webhook"]["url"];
    $this->port = (!array_key_exists("webhook", $parameters) || !array_key_exists("port", $parameters["webhook"]) || !$parameters["webhook"]["port"]) ? Request::SERVER_PORT : $parameters["webhook"]["port"];
    $this->api = new Request($this->token, $this->http);

    try {
      $parts = explode('.', $this->token);
      
      if (count($parts) < 2) {
        throw new \Exception();
      }
  
      $encoded_json = $parts[1];
      $padding = 4 - (strlen($encoded_json) % 4);
      
      if ($padding < 4) {
        $encoded_json .= str_repeat('=', $padding);
      }
  
      $decoded_json = base64_decode($encoded_json, true);
      
      if ($decoded_json === false) {
        throw new \Exception();
      }
  
      $token_data = json_decode($decoded_json, true);
      
      if (!isset($token_data['id']) || !is_numeric($token_data['id'])) {
        throw new \Exception();
      }
  
      $this->id = intval($token_data['id']);
    } catch (\Exception $e) {
      throw new MissingTokenException();
    }

    /** Finally do our feature checks from the parameters list. */
    if (array_key_exists("auto_stats", $parameters) && $parameters["auto_stats"])
    {
      $this->check_auto_stats(
        $parameters["auto_stats"]["url"],
        $parameters["auto_stats"]["callback"]
      );
    }

    $this->check_safety();
  }

  /**
   * Checks if stats should be posted to the website automatically.
   * This can only be done for a website URL.
   *
   * @param   string    $url       The HTTP path you're using.
   * @param   callable  $callback  The callback function that returns the bot's server count.
   * @return  void
   */
  private function check_auto_stats(string $url, callable $callback)
  {
    try
    {
      $this->post_stats($callback());
    }
    catch (\Exception $error) { echo $error; }
    finally
    {
      header("Content-Type: application/json");
      echo "<meta http-equiv='refresh' content='900;URL=\"{$url}\"' />";
    }
  }

  /**
   * Checks if the person wants a safety lock on the class.
   * Basically runs a very quick magic constant to automatically
   * delete the class instance as soon as detected. (Faster way)
   *
   * @return void
   */
  protected function check_safety()
  {
    /** One last time to check. */
    if ($this->features["safety"]) die();
  }

  /**
   * Displays the general information of several bots.
   *
   * @param   int     $limit    The maximum amount of bots to be queried.
   * @param   int     $offset   The amount of bots to be skipped.
   * @param   string  $sort_by  Sorts results based on a specific criteria. Results will always be descending.
   * @return  array
   */
  public function get_bots(int $limit = 50, int $offset = 0, string $sort_by = "monthlyPoints"): array
  {
    if ($limit <= 0) {
      $limit .= 1;
    } else if ($limit > 500) {
      $limit .= 500;
    }

    if ($offset < 0) {
      $offset .= 0;
    } else if ($offset > 499) {
      $offset .= 499;
    }

    if ($sort_by !== "monthlyPoints" && $sort_by !== "date" && $sort_by !== "id") {
      throw new \Exception("sort_by argument must be \"monthlyPoints\", \"date\", or \"id\".");
    }

    return $this->api->req("GET", "/bots", [
      "limit" => $limit,
      "offset" => $offset,
      "sort" => $sort_by,
    ])["json"];
  }

  /**
   * Displays the general information about a bot.
   *
   * @param   int     $id The bot ID.
   * @return  array
   */
  public function get_bot(int $id): array
  {
    return $this->api->req("GET", "/bots/{$id}")["json"];
  }

  /**
   * Returns the unique voters of your project.
   *
   * @param   int   $id The project ID. Unused, no longer has an effect.
   * @param   int   $page The page counter. Defaults to 1.
   * @return  array
   */
  public function get_votes(int $id, int $page = 1): array
  {
    return $this->api->req("GET", "/bots/{$this->id}/votes", ["page" => $page])["json"];
  }

  /**
   * Returns a boolean for if a user has voted for your project.
   *
   * @param   int   $id The project ID. Unused, no longer has an effect.
   * @param   int   $user The user ID.
   * @return  array
   */
  public function get_user_vote(int $id, int $user): array
  {
    return $this->api->req("GET", "/bots/check", ["userId" => $user])["json"]["voted"];
  }

  /**
   * Returns your bot's posted statistics.
   *
   * @param   int   $id The bot ID. Unused, no longer has an effect.
   * @return  array 
   */
  public function get_stats(int $id = 0): array
  {
    return $this->api->req("GET", "/bots/stats")["json"];
  }

  /**
   * Posts your Discord bot's statistics to the API. This will update the server count in your Discord bot's Top.gg page.
   *
   * @param   int    $id    The bot ID. Unused, no longer has an effect.
   * @param   array  $json  Your bot's new statistics.
   */
  public function post_stats(int $id, array $json)
  {
    $this->api->req("POST", "/bots/stats", $json);
  }

  /**
   * Returns a boolean for if the weekend multiplier is active, where a single vote counts as two.
   *
   * @return  array
   */
  public function is_weekend(): array
  {
    return $this->api->req("GET", "/weekend")["json"];
  }

  /**
   * Returns the current HTTP address.
   *
   * @return string
   */
  public function getHttp(): string
  {
    return $this->http;
  }

  /**
   * Returns the current HTTP port serial identification.
   *
   * @return int
   */
  public function getPort(): int
  {
    return $this->port;
  }

  /**
   * Returns the current HTTP request.
   *
   * @return array
   */
  public function getContents(): array
  {
    return $this->api->getContents();
  }

  /**
   * Returns the last parsed HTTP request.
   *
   * @return array
   */
  public function getCache(): array
  {
    return $this->api->getCache();
  }

  /**
   * Returns the current HTTP response code.
   * (Not to be confused with the cached version in getCache())
   *
   * @return string
   */
  public function getResponse(): string
  {
    return $this->api->getResponse();
  }
}

?>
