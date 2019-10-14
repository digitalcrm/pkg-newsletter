<?php

namespace Digitalcrm\Newsletter;

use DrewM\MailChimp\MailChimp;
use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider
{
	protected $defer = false;

	public function boot()
	{
		// Load route package
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		// Load views package
		$this->loadViewsFrom(__DIR__.'/views','newsletter');
		//Asset Publish
		$this->publishes([
			__DIR__.'/assets' => public_path('newsletter'),
		], 'assets');
		// Load migrations package
		$this->loadMigrationsFrom(__DIR__.'/database/migrations');
		// merge
		$this->mergeConfigFrom(__DIR__.'/config/newsletter.php','newsletter');
		//publishes config
		$this->publishes([
			__DIR__.'/config/newsletter.php' => config_path('newsletter.php'),
		]);
	}

	public function register()
	{
		$this->app->singleton(Newsletter::class, function () {
			$driver = config('newsletter.driver', 'api');
			if (is_null($driver) || $driver === 'log') {
				return new NullDriver($driver === 'log');
			}

			$mailChimp = new Mailchimp(config('newsletter.apiKey'));

			$mailChimp->verify_ssl = config('newsletter.ssl', true);

			$configuredLists = NewsletterListCollection::createFromConfig(config('newsletter'));

			return new Newsletter($mailChimp, $configuredLists);
		});

		$this->app->alias(Newsletter::class, 'newsletter');
	}
}