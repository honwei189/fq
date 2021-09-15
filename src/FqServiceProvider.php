<?php
/*
 * @creator           : Gordon Lim <honwei189@gmail.com>
 * @created           : 05/05/2019 17:45:39
 * @last modified     : 06/06/2020 15:27:51
 * @last modified by  : Gordon Lim <honwei189@gmail.com>
 */

namespace honwei189\Flayer\Fq;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

/**
 *
 * Service provider (for Laravel)
 *
 *
 * @package     Fq
 * @subpackage
 * @author      Gordon Lim <honwei189@gmail.com>
 * @link        https://github.com/honwei189/fq/
 * @version     "1.0.0"
 * @since       "1.0.0"
 */
class FqServiceProvider extends ServiceProvider
{
   /**
     * Register service
     *
     * @return void
     */
    public function register()
    {
        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('fq', fq::class);
        });
    }

    /**
     * Load service on start-up
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('fq', function () {
            return new Fq;
        });
    }

    public function provides()
    {
        return [Fq::class];
    }
}
