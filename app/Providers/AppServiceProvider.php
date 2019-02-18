<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot() {
    Blade::directive('is_profile_tweets', function ($url_current) {
      return ((strpos($url_current, 'following') === false) && (strpos($url_current, 'followers') === false)) ? 'active' : '';
    });
    Blade::directive('digit02', function ($int) {
      return "<?php echo sprintf('%02d', $int); ?>";
    });
    Blade::directive('hoge', function () {
      return true;
    });
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register() {
    //
  }
}
