# Extended Route List for Laravel

This package extends default command `route:list` to include docblocks `@see @author @version @access @param @return`. Provides config file to setup what columns, how, and when to show.

# Example

Declared index function with docblock information inside `AppleController`
```php
/**
 * Display a listing of all apples that currently logged in user has in his basket.
 *
 * @see https://docs.localhost/api/resources/apples.html#index
 * @since v.1.0
 * @access public
 * 
 * @return \Illuminate\Http\JsonResponse
 */
public function index()
{
    return ApplesResource::collection(auth()->user()->apples()->paginate());
}
```

the web.php route only contains normal route

```php
Route::resource('/apples', AppleController::class);
```

```bash
php artisan route:list --path=apples
```

will return

```
+--------+-----------+---------------------+----------------+-------------------------+------------+--------------------------------------------------------+
| Domain | Method    | URI                 | Name           | Action                  | Middleware | See                                                    |
+--------+-----------+---------------------+----------------+-------------------------+------------+--------------------------------------------------------+
|        | GET|HEAD  | apples              | apples.index   | AppleController@index   | web        | https://docs.localhost/api/resources/apples.html#index |
|        | POST      | apples              | apples.store   | AppleController@store   | web        |                                                        |
|        | GET|HEAD  | apples/create       | apples.create  | AppleController@create  | web        |                                                        |
|        | GET|HEAD  | apples/{apple}      | apples.show    | AppleController@show    | web        |                                                        |
|        | PUT|PATCH | apples/{apple}      | apples.update  | AppleController@update  | web        |                                                        |
|        | DELETE    | apples/{apple}      | apples.destroy | AppleController@destroy | web        |                                                        |
|        | GET|HEAD  | apples/{apple}/edit | apples.edit    | AppleController@edit    | web        |                                                        |
+--------+-----------+---------------------+----------------+-------------------------+------------+--------------------------------------------------------+
```

# Installation

```bash
composer require on3n3o/extended-route-list
```

## Publish config file or use .env variables

The file will be located in `config/extended-route-list.php`

```bash
php artisan vendor:publish --provider="On3n3o\\ExtendedRouteList\\ExtendedRouteListServiceProvider"
```

Or you can use `.env` variables

```
EXT_ROUTE_LIST_ACTION_FORMAT="short" # short or full
EXT_ROUTE_LIST_MIDDLEWARE_FORMAT="short" # short or full
EXT_ROUTE_LIST_MIDDLEWARE_LINESTYLE="single" # single or multi
EXT_ROUTE_LIST_JSON_DROP_COLUMN_IF_EMPTY=true # if used `--json` in command and column returns null or [] then drop it form output
EXT_ROUTE_LIST_JSON_OPTIONS=192 #CHECK THIS IF SHOULD BE INTEGER VALUE https://www.php.net/manual/en/json.constants.php
```

> If you want to show all columns you can use `--all-columns` or `-a`

> If you want to show compact columns you can use `--compact` or `-c`

> If you need to export to json use `--json`

## Customization of columns

To customize your column layout or wich should when be displayed you NEED to publish config.

## Available docblock signatures

```php
/**
 * Class HomeController

 * @package App\Http\Controllers
 * @author  Marcin Maciejewski
 * @version v.1.2
 * @since   v.0.9
 * @access public
 * @link https://example.org/home
 * @see https://docs.localhost/api/HomeController
 * @see https://laravel.com/docs/8.x/controllers
 * @example api.get(`/home`)
 * @deprecated true
 * 
 * @todo Better description
 * @todo Better description for class name
 * @fixme Better description for testing
 * 
 * @inheritdoc parent::__construct()
 * @license MIT
 * @fixme now
 */
class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @uses auth()->user()
     * @see https://laravel.com/docs/5.4/controllers#method-index
     * @since v.1.0
     * @access public
     * 
     * @param \Illuminate\Http\Request
     * 
     * @return \Illuminate\Http\Response
     * 
     * @throws \Exception
     */
    public function index(Request $request)
    {
        //
    }
```

## Setting up unusual docblock signatures

To do this you need to publish config and change inside `config/extended-route-list.php` where 'signature' is your new docblock signature.

```php

return [
    ...
    'casts' => [
        ...
        'signature' => [
            'column_name' => 'signature',
            'tags' => ['@signature1', '@sig1'],
            'linestyle' => 'single'
        ]
    ],
    ...
    'columns' => [
        ...
        'Signature',
    ],
    'normal_columns' => [
        ...
        'Signature',
    ],
    // if you want to show in compact columns add whats below
    'compact_columns' => [
        ...
        'Signature',
    ],
    // if you want to show in all columns add whats below
    'all_columns' => [
        ...
        'Signature',
    ]

];
```