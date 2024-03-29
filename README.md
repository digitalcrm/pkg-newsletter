# pkg-newsletter
```Package Uses:
Install package
Create Controller, view, and rotues for this
Run
```
This package provides an easy way to integrate MailChimp with Laravel "5.8.*".

```php
// at the top of your class
use Newsletter;

// ...

Newsletter::subscribe('rincewind@discworld.com');

Newsletter::unsubscribe('the.luggage@discworld.com');

//Merge variables can be passed as the second argument
Newsletter::subscribe('sam.vines@discworld.com', ['firstName'=>'Sam', 'lastName'=>'Vines']);

//Subscribe someone to a specific list by using the third argument:
Newsletter::subscribe('nanny.ogg@discworld.com', ['firstName'=>'Nanny', 'lastName'=>'Ogg'], 'Name of your list');

//Subscribe someone to a specific list and require them to confirm via email:
Newsletter::subscribePending('nanny.ogg@discworld.com', ['firstName'=>'Nanny', 'lastName'=>'Ogg'], 'Name of your list');

//Subscribe or update someone
Newsletter::subscribeOrUpdate('sam.vines@discworld.com', ['firstName'=>'Foo', 'lastName'=>'Bar']);

// Change the email address of an existing subscriber
Newsletter::updateEmailAddress('rincewind@discworld.com', 'the.luggage@discworld.com');

//Get some member info, returns an array described in the official docs
Newsletter::getMember('lord.vetinari@discworld.com');

//Get the member activity, returns an array with recent activity for a given user
Newsletter::getMemberActivity('lord.vetinari@discworld.com');

//Get the members for a given list, optionally filtered by passing a second array of parameters
Newsletter::getMembers();

//Check if a member is subscribed to a list
Newsletter::isSubscribed('rincewind@discworld.com');

//Returns a boolean
Newsletter::hasMember('greebo@discworld.com');

// Get the tags for a member in a given list
Newsletter::getTags('lord.vetinari@discworld.com');

// Add tags for a member in a given list, any new tags will be created
Newsletter::addTags(['tag-1', 'tag-2'], 'lord.vetinari@discworld.com');

// Remove tags for a member in a given list
Newsletter::removeTags(['tag-1', 'tag-2'], 'lord.vetinari@discworld.com');

//If you want to do something else, you can get an instance of the underlying API:
Newsletter::getApi();
```
## Installation

You can install this package via composer using:

```bash
composer require digitalcrm/newsletter
```

The package will automatically register itself.

To publish the config file to `config/newsletter.php` run:

```bash
php artisan vendor:publish --provider="Digitalcrm\Newsletter\NewsletterServiceProvider"
```

This will publish a file `newsletter.php` in your config directory with the following contents:
```php
return [

    /*
     * The driver to use to interact with MailChimp API.
     * You may use "log" or "null" to prevent calling the
     * API directly from your environment.
     */
    'driver' => env('MAILCHIMP_DRIVER', 'api'),

    /*
     * The API key of a MailChimp account. You can find yours at
     * https://us10.admin.mailchimp.com/account/api-key-popup/.
     */
    'apiKey' => env('MAILCHIMP_APIKEY'),

    /*
     * The listName to use when no listName has been specified in a method.
     */
    'defaultListName' => 'subscribers',

    /*
     * Here you can define properties of the lists.
     */
    'lists' => [

        /*
         * This key is used to identify this list. It can be used
         * as the listName parameter provided in the various methods.
         *
         * You can set it to any string you want and you can add
         * as many lists as you want.
         */
        'subscribers' => [

            /*
             * A MailChimp list id. Check the MailChimp docs if you don't know
             * how to get this value:
             * http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id.
             */
            'id' => env('MAILCHIMP_LIST_ID'),
        ],
    ],

    /*
     * If you're having trouble with https connections, set this to false.
     */
    'ssl' => true,
];
```
## Usage

After you've installed the package and filled in the values in the config-file working with this package will be a breeze. All the following examples use the facade. Don't forget to import it at the top of your file.

```php
use Newsletter;
```

### Subscribing, updating and unsubscribing

Subscribing an email address can be done like this:

```php
use Newsletter;

Newsletter::subscribe('rincewind@discworld.com');
```

Let's unsubscribe someone:

```php
Newsletter::unsubscribe('the.luggage@discworld.com');
```

You can pass some merge variables as the second argument:
```php
Newsletter::subscribe('rincewind@discworld.com', ['firstName'=>'Rince', 'lastName'=>'Wind']);
```
> Please note the at the time of this writing the default merge variables in MailChimp are named `FNAME` and `LNAME`. In our examples we use `firstName` and `lastName` for extra readability.

You can subscribe someone to a specific list by using the third argument:
```php
Newsletter::subscribe('rincewind@discworld.com', ['firstName'=>'Rince', 'lastName'=>'Wind'], 'subscribers');
```
That third argument is the name of a list you configured in the config file.

You can also subscribe and/or update someone. The person will be subscribed or updated if he/she is already subscribed:

 ```php
 Newsletter::subscribeOrUpdate('rincewind@discworld.com', ['firstName'=>'Foo', 'lastname'=>'Bar']);
 ```

You can subscribe someone to one or more specific group(s)/interest(s) by using the fourth argument:

```php
Newsletter::subscribeOrUpdate('rincewind@dscworld.com', ['firstName'=>'Rince','lastName'=>'Wind'], 'subscribers', ['interests'=>['interestId'=>true, 'interestId'=>true]])
```
Simply add `false` if you want to remove someone from a group/interest.

You can also unsubscribe someone from a specific list:
```php
Newsletter::unsubscribe('rincewind@discworld.com', 'subscribers');
```

### Deleting subscribers

Deleting is not the same as unsubscribing. Unlike unsubscribing, deleting a member will result in the loss of all history (add/opt-in/edits) as well as removing them from the list. In most cases you want to use `unsubscribe` instead of `delete`.

Here's how to perform a delete:

```php
Newsletter::delete('rincewind@discworld.com');
```

### Deleting subscribers permanently

Delete all personally identifiable information related to a list member, and remove them from a list. This will make it impossible to re-import the list member.

Here's how to perform a permanent delete:

```php
Newsletter::deletePermanently('rincewind@discworld.com');
```

### Getting subscriber info

You can get information on a subscriber by using the `getMember` function:
```php
Newsletter::getMember('lord.vetinari@discworld.com');
```

This will return an array with information on the subscriber. If there's no one subscribed with that
e-mail address the function will return `false`

There's also a convenience method to check if someone is already subscribed:

```php
Newsletter::hasMember('nanny.ogg@discworld.com'); //returns a boolean
```

In addition to this you can also check if a user is subscribed to your list:

```php
Newsletter::isSubscribed('lord.vetinari@discworld.com'); //returns a boolean
```

### Creating a campaign

This the signature of `createCampaign`:
```php
public function createCampaign(
    string $fromName,
    string $replyTo,
    string $subject,
    string $html = '',
    string $listName = '',
    array $options = [],
    array $contentOptions = [])
```

Note the campaign will only be created, no mails will be sent out.

### Handling errors

If something went wrong you can get the last error with:
```php
Newsletter::getLastError();
```

If you just want to make sure if the last action succeeded you can use:
```php
Newsletter::lastActionSucceeded(); //returns a boolean
```

### Need something else?

If you need more functionality you get an instance of the underlying [MailChimp Api](https://github.com/drewm/mailchimp-api) with:

```php
$api = Newsletter::getApi();
```
