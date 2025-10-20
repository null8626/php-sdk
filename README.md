# Top.gg PHP SDK

The community-maintained PHP library for Top.gg.

## Chapters

- [Installation](#installation)
- [Setting up](#setting-up)
- [Usage](#usage)
  - [API v1](#api-v1-1)
    - [Getting your project's vote information of a user](#getting-your-projects-vote-information-of-a-user)
    - [Posting your bot's application commands list](#posting-your-bots-application-commands-list)
  - [API v0](#api-v0-1)
    - [Getting a bot](#getting-a-bot)
    - [Getting several bots](#getting-several-bots)
    - [Getting your project's voters](#getting-your-projects-voters)
    - [Check if a user has voted for your project](#check-if-a-user-has-voted-for-your-project)
    - [Getting your bot's statistics](#getting-your-bots-statistics)
    - [Posting your bot's statistics](#posting-your-bots-statistics)
    - [Automatically posting your bot's statistics every few minutes](#automatically-posting-your-bots-statistics-every-few-minutes)
    - [Checking if the weekend vote multiplier is active](#checking-if-the-weekend-vote-multiplier-is-active)
    - [Generating widget URLs](#generating-widget-urls)
  - [Webhooks](#webhooks)
    - [Being notified whenever someone voted for your project](#being-notified-whenever-someone-voted-for-your-project)

## Installation

```sh
$ composer require top-gg/php-sdk
```

## Setting up

### API v1

> **NOTE**: API v1 also includes API v0.

```php
include_once __DIR__ . "/vendor/autoload.php";

use DBL\V1DBL;

$client = new V1DBL([
  "token" => "Top.gg API token"
]);
```

### API v0

```php
include_once __DIR__ . "/vendor/autoload.php";

use DBL\DBL;

$client = new DBL([
  "token" => "Top.gg API token"
]);
```

## Usage

### API v1

#### Getting your project's vote information of a user

```php
$vote = $client->get_vote(661200758510977084);
```

#### Posting your bot's application commands list

```php
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
```

### API v0

#### Getting a bot

```php
$bot = $client->get_bot(1026525568344264724);
```

#### Getting several bots

```php
//                        Limit  Offset  Sort by
$bots = $client->get_bots(50,    0,      "monthlyPoints");
```

#### Getting your project's voters

##### First page

```php
$voters = $client->get_votes();
```

##### Subsequent pages

```php
//                           Bot ID (unused)  Page number
$voters = $client->get_votes(0,               2);
```

#### Check if a user has voted for your project

```php
//                                  Bot ID (unused)  User ID
$has_voted = $client->get_user_vote(0,               661200758510977084);
```

#### Getting your bot's statistics

```php
$stats = $client->get_stats();
```

#### Posting your bot's statistics

```php
$client->post_stats(0, [
  "server_count" => $bot->get_server_count()
]);
```

#### Automatically posting your bot's statistics every few minutes

> **NOTE**: Considering PHP's shortcomings, this is only possible via a URL.

In your original client declaration:

```php
$client = new DBL([
  "token" => "Insert your Top.gg API token here.",
  "auto_stats" => [
    "url": "Your URL",
    "callback": => function () use ($bot) {
      return [
        "server_count" => $bot->get_server_count()
      ];
    }
  ]
]);
```

#### Checking if the weekend vote multiplier is active

```php
$is_weekend = $client->is_weekend();
```

#### Generating widget URLs

##### Large

```php
use DBL\Widget;
use DBL\WidgetType;

$widget_url = Widget::large(WidgetType::DiscordBot, 574652751745777665);
```

##### Votes

```php
use DBL\Widget;
use DBL\WidgetType;

$widget_url = Widget::votes(WidgetType::DiscordBot, 574652751745777665);
```

##### Owner

```php
use DBL\Widget;
use DBL\WidgetType;

$widget_url = Widget::owner(WidgetType::DiscordBot, 574652751745777665);
```

##### Social

```php
use DBL\Widget;
use DBL\WidgetType;

$widget_url = Widget::social(WidgetType::DiscordBot, 574652751745777665);
```

### Webhooks

#### Being notified whenever someone voted for your project

**With Laravel:**

In your `config/logging.php`:

```php
"channels" => [
  "stdout" => [
    "driver" => "single",
    "path" => "php://stdout",
    "level" => "debug"
  ]
]
```

In your `routes/api.php`:

```php
use DBL\Webhook;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class MyVoteListener extends Webhook {
  public function getAuthorization() {
    return getenv("MY_TOPGG_WEBHOOK_SECRET");
  }

  public function callback(array $vote) {
    Log::channel("stdout")->info("A user with the ID of " . $vote["user"] . " has voted us on Top.gg!");
  }
}

Route::post('/votes', [MyVoteListener::class, "main"]);
```
