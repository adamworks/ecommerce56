<?php

namespace main;

use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;

class MainServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleFailedJobs();
        $this->oneSignalChannel();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('core.support.option', Support\Option\OptionService::class);
        $this->app->singleton('core.support.uploader', Support\Uploader\UploaderService::class);
        $this->app->singleton('core.support.modelMeta', Support\ModelMeta\ModelMeta::class);
        $this->app->singleton('core.support.formatter', Support\Formatter\FormatterService::class);
    }

    /**
     * Register any failed jobs.
     *
     * @return void
     */
    private function handleFailedJobs()
    {
        Queue::failing(function (JobFailed $event) {
            \Log::info($event->connectionName);
            \Log::info($event->job);
            \Log::info($event->exception);
        });
    }

    /**
     * One signal channel.
     *
     * @return void
     */
    private function oneSignalChannel()
    {
        $this->app->when(Support\Notification\OneSignal\OneSignalChannel::class)
            ->needs(\Berkayk\OneSignal\OneSignalClient::class)
            ->give(function () {
                $oneSignalConfig = config('onesignal');

                if (is_null($oneSignalConfig)) {
                    throw Support\Notification\OneSignal\InvalidConfiguration::configurationNotSet();
                }

                return new \Berkayk\OneSignal\OneSignalClient(
                    $oneSignalConfig['app_id'],
                    $oneSignalConfig['rest_api_key'],
                    ''
                );
            });
    }
}
