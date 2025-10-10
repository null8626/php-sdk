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

/**
 * This file is to serve as an example about how the library
 * is able to be used with respect to the functions of the
 * Top.gg API. Below is a sample script for how to make it
 * work.
 */

include_once __DIR__ . "/vendor/autoload.php";
use DBL\V1DBL;

$token = @file_get_contents(".TOKEN");
$client = new V1DBL([
  "token" => $token
]);

echo "\nrunning get bot:\n";
$bot = $client->get_bot(1026525568344264724);
print_r($bot);

echo "\nrunning get bots:\n";
$bots = $client->get_bots();
print_r($bots);

echo "\nrunning get votes:\n";
$voters = $client->get_votes();
print_r($voters);

echo "\nrunning get user vote:\n";
$has_voted = $client->get_user_vote(0, 661200758510977084);
print_r($has_voted);

echo "\nrunning get stats:\n";
$stats = $client->get_stats();
print_r($stats);

echo "\nrunning post stats:\n";
$client->post_stats(0, [
  "server_count" => 2
]);

echo "\nrunning is weekend:\n";
$is_weekend = $client->is_weekend();
print_r($is_weekend);

echo "\nrunning post commands:\n";
$client->post_commands([
  [
    "options" => [],
    "name" => "test",
    "name_localizations" => null,
    "description" => "command description",
    "description_localizations" => null,
    "contexts" => [],
    "default_permission" => null,
    "default_member_permissions" => null,
    "dm_permission" => false,
    "integration_types" => [],
    "nsfw" => false
  ]
]);

echo "\nrunning get vote:\n";
$vote = $client->get_vote(661200758510977084);
print_r($vote);

?>
