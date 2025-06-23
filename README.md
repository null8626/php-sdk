# Top.gg PHP SDK

The community-maintained PHP library for Top.gg.

## Installation

```sh
$ composer require top-gg/php-sdk
```

## Setting up

```php
include_once __DIR__ . "/vendor/autoload.php";

use DBL\DBL;

$client = new DBL([
  "token" => "Insert your Top.gg API token here."
]);
```

## Usage

### Getting a bot

```php
$bot = $client->get_bot();
```

### Getting several bots

#### With defaults

```php
$bots = $client->get_bots();
```

#### With explicit arguments

```php
//                        Limit  Offset  Sort by
$bots = $client->get_bots(50,    0,      "date");
```

### Getting your bot's voters

#### First page

```php
$voters = $client->get_voters();
```

#### Subsequent pages

```php
$voters = $client->get_voters(2);
```

### Check if a user has voted for your bot

```php
$has_voted = $client->has_voted(205680187394752512);
```

### Getting your bot's server count

```php
$server_count = $client->get_server_count();
```

### Posting your bot's server count

```php
$client->post_server_count($bot->get_server_count());
```

### Automatically posting your bot's server count every few minutes

> **NOTE:** Considering PHP's shortcomings, this is only possible via a URL.

In your original client declaration:

```php
$client = new DBL([
  "token" => "Insert your Top.gg API token here.",
  "auto_stats" => [
    "url": "Your URL",
    "callback": => function () use ($bot) {
      return $bot->get_server_count();
    }
  ]
]);
```

### Checking if the weekend vote multiplier is active

```php
$is_weekend = $client->is_weekend();
```

### Generating widget URLs

#### Large

```php
use DBL\Widget;
use DBL\WidgetType;

$widget_url = Widget::large(WidgetType::DiscordBot, 574652751745777665);
```

#### Votes

```php
use DBL\Widget;
use DBL\WidgetType;

$widget_url = Widget::votes(WidgetType::DiscordBot, 574652751745777665);
```

#### Owner

```php
use DBL\Widget;
use DBL\WidgetType;

$widget_url = Widget::owner(WidgetType::DiscordBot, 574652751745777665);
```

#### Social

```php
use DBL\Widget;
use DBL\WidgetType;

$widget_url = Widget::social(WidgetType::DiscordBot, 574652751745777665);
```

### Webhooks

#### Being notified whenever someone voted for your bot

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