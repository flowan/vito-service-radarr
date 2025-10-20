<?php

namespace App\Vito\Plugins\Flowan\VitoServiceRadarr;

use App\Plugins\AbstractPlugin;
use App\Plugins\RegisterServiceType;
use App\Plugins\RegisterViews;
use App\Vito\Plugins\Flowan\VitoServiceRadarr\Services\Radarr;
use Illuminate\Support\Facades\Artisan;

class Plugin extends AbstractPlugin
{
    protected string $name = 'Radarr';

    protected string $description = 'Radarr is a movie collection manager for Usenet and BitTorrent users.';

    public function boot(): void
    {
        RegisterViews::make('vito-service-radarr')
            ->path(__DIR__.'/views')
            ->register();

        RegisterServiceType::make('radarr')
            ->type(Radarr::type())
            ->label($this->name)
            ->handler(Radarr::class)
            ->versions([
                'latest',
            ])
            ->register();
    }

    public function enable(): void
    {
        // Temporary fix until this is fixed in vito, see https://github.com/vitodeploy/vito/issues/842
        dispatch(fn () => Artisan::call('horizon:terminate'));
    }
}
