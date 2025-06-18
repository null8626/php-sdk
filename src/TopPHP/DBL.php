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

    error_reporting(0);

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

    if ($parameters["auto_stats"]) $this->features["auto_stats"][0] = true;
    if ($parameters["safety"]) $this->features["safety"] = true;
    if ($parameters["webhook"]) $this->features["webhook"] = true;
    if ($parameters["token"]) $this->token = $parameters["token"];
    else throw new MissingTokenException();

    $this->http = (!$parameters["webhook"]["url"]) ? Request::SERVER_ADDR : $parameters["webhook"]["url"];
    $this->port = (!$parameters["webhook"]["port"]) ? Request::SERVER_PORT : $parameters["webhook"]["port"];

    error_reporting(E_ALL);

    $this->api = new Request($this->token, $this->http);

    try {
      $parts = explode('.', $this->token);
      
      if (count($parts) < 2) {
        throw new Exception();
      }
  
      $encoded_json = $parts[1];
      $padding = 4 - (strlen($encoded_json) % 4);
      
      if ($padding < 4) {
        $encoded_json .= str_repeat('=', $padding);
      }
  
      $decoded_json = base64_decode($encoded_json, true);
      
      if ($decoded_json === false) {
        throw new Exception();
      }
  
      $token_data = json_decode($decoded_json, true);
      
      if (!isset($token_data['id']) || !is_numeric($token_data['id'])) {
        throw new Exception();
      }
  
      $this->id = intval($token_data['id']);
    } catch (Exception $e) {
      throw new MissingTokenException();
    }

    error_reporting(0);

    /** Finally do our feature checks from the parameters list. */
    if ($parameters["auto_stats"])
    {
      $this->check_auto_stats(
        $parameters["auto_stats"]["url"]
      );
    }

    error_reporting(E_ALL);

    $this->check_safety();
  }

  /**
   * Checks if stats should be posted to the website automatically.
   * This can only be done for a website URL.
   *
   * @param   string  $path     The HTTP path you're using.
   * @param   array   $values   A list of values to be automatically posted.
   * @return  void
   */
  protected function check_auto_stats(string $path, array $values)
  {
    try
    {
      if ($values["server_count"]) $_json["server_count"]  = $values["server_count"];
      else throw new MissingStatsException();

      $_url = ($path) ? $path : throw new MissingStatsException();
      $_request = $this->api->req("POST", "/bots/stats", $_json)["json"];
    }

    catch(Exception $error) { echo $error; }

    finally
    {
      header("Content-Type: application/json");
      echo "<meta http-equiv='refresh' content='900;URL=\"{$_url}\"' />";
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
   * @return  array
   */
  public function get_bots(int $limit = 50, int $offset = 0, string $sort_by = "monthlyPoints"): array
  {
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
   * Returns the unique voters of the bot.
   *
   * @param   int   $page The page counter. Starts from 1.
   * @return  array
   */
  public function get_votes(int $page = 1): array
  {
    return $this->api->req("GET", "/bots/{$this->id}/votes", ["page" => $page])["json"];
  }

  /**
   * Returns a boolean check for if a user voted for your bot.
   *
   * @param   int   $user The user Snowflake ID.
   * @return  array
   */
  public function get_user_vote(int $user): array
  {
    return $this->api->req("GET", "/bots/check", ["userId" => $user])["json"]["voted"];
  }

  /**
   * Returns a boolean check for if the weekend multiplier is active, where a single vote counts as two.
   *
   * @return  array
   */
  public function is_weekend(): array
  {
    return $this->api->req("GET", "/weekend")["json"]["is_weekend"];
  }

  /**
   * Returns the statistics of the bot.
   *
   * @return  array
   */
  public function get_stats(): array
  {
    return $this->api->req("GET", "/bots/stats")["json"];
  }

  /**
   * Posts statistics to the bot's Top.gg page.
   *
   * @param   array $json The JSON query fields.
   * @return  array
   */
  public function post_stats(array $json): array
  {
    return $this->api->req("POST", "/bots/stats", $json)["json"];
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
