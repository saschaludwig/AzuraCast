<?php

/**
 * Administrative dashboard configuration.
 */

declare(strict_types=1);

use App\Enums\StationFeatures;
use App\Enums\StationPermissions;
use App\Radio\Enums\AudioProcessingMethods;

return static function (App\Event\BuildStationMenu $e) {
    $request = $e->getRequest();
    $station = $e->getStation();

    $backendConfig = $station->getBackendConfig();

    $router = $request->getRouter();

    $settings = $e->getSettings();

    $hasLocalServices = $station->hasLocalServices();

    $e->merge(
        [
            'start_station' => [
                'label' => __('Start Station'),
                'title' => __('Ready to start broadcasting? Click to start your station.'),
                'icon' => 'refresh',
                'url' => $router->fromHere('stations:restart:index'),
                'class' => 'text-success',
                'visible' => $hasLocalServices && !$station->getHasStarted(),
                'permission' => StationPermissions::Broadcasting,
            ],
            'restart_station' => [
                'label' => __('Reload to Apply Changes'),
                'icon' => 'refresh',
                'url' => $router->fromHere('stations:restart:index'),
                'class' => 'text-warning btn-restart-station '
                    . (!$station->getNeedsRestart() ? 'd-none' : ''),
                'visible' => $hasLocalServices && $station->getHasStarted(),
                'permission' => StationPermissions::Broadcasting,
            ],
            'profile' => [
                'label' => __('Profile'),
                'icon' => 'image',
                'items' => [
                    [
                        'label' => __('View Profile'),
                        'url' => $router->fromHere('stations:profile:index'),
                    ],
                    [
                        'label' => __('Edit Profile'),
                        'url' => $router->fromHere('stations:profile:edit'),
                        'permission' => StationPermissions::Profile,
                    ],
                    [
                        'label' => __('Branding'),
                        'url' => $router->fromhere('stations:branding'),
                        'permission' => StationPermissions::Profile,
                    ],
                ],
            ],
            'public' => [
                'label' => __('Public Page'),
                'icon' => 'public',
                'url' => $router->named('public:index', ['station_id' => $station->getShortName()]),
                'external' => true,
                'visible' => $station->getEnablePublicPage(),
            ],
            'media' => [
                'label' => __('Media'),
                'icon' => 'library_music',
                'visible' => StationFeatures::Media->supportedForStation($station),
                'items' => [
                    'files' => [
                        'label' => __('Music Files'),
                        'icon' => 'library_music',
                        'url' => $router->fromHere('stations:files:index'),
                        'permission' => StationPermissions::Media,
                    ],
                    'reports_duplicates' => [
                        'label' => __('Duplicate Songs'),
                        'url' => $router->fromHere('stations:files:index') . '#special:duplicates',
                        'permission' => StationPermissions::Media,
                    ],
                    'reports_unprocessable' => [
                        'label' => __('Unprocessable Files'),
                        'url' => $router->fromHere('stations:files:index') . '#special:unprocessable',
                        'permission' => StationPermissions::Media,
                    ],
                    'reports_unassigned' => [
                        'label' => __('Unassigned Files'),
                        'url' => $router->fromHere('stations:files:index') . '#special:unassigned',
                        'permission' => StationPermissions::Media,
                    ],
                    'ondemand' => [
                        'label' => __('On-Demand Media'),
                        'icon' => 'cloud_download',
                        'url' => $router->named('public:ondemand', ['station_id' => $station->getShortName()]),
                        'external' => true,
                        'visible' => $station->getEnableOnDemand(),
                    ],
                    'sftp_users' => [
                        'label' => __('SFTP Users'),
                        'url' => $router->fromHere('stations:sftp_users:index'),
                        'visible' => StationFeatures::Sftp->supportedForStation($station),
                        'permission' => StationPermissions::Media,
                    ],
                    'bulk_media' => [
                        'label' => __('Bulk Media Import/Export'),
                        'url' => $router->fromHere('stations:bulk-media'),
                        'permission' => StationPermissions::Media,
                    ],
                ],
            ],

            'playlists' => [
                'label' => __('Playlists'),
                'icon' => 'queue_music',
                'url' => $router->fromHere('stations:playlists:index'),
                'visible' => StationFeatures::Media->supportedForStation($station),
                'permission' => StationPermissions::Media,
            ],

            'podcasts' => [
                'label' => __('Podcasts'),
                'icon' => 'cast',
                'url' => $router->fromHere('stations:podcasts:index'),
                'visible' => StationFeatures::Podcasts->supportedForStation($station),
                'permission' => StationPermissions::Podcasts,
            ],

            'live_streaming' => [
                'label' => __('Live Streaming'),
                'icon' => 'mic',
                'visible' => StationFeatures::Streamers->supportedForStation($station),
                'items' => [
                    'streamers' => [
                        'label' => __('Streamer/DJ Accounts'),
                        'icon' => 'mic',
                        'url' => $router->fromHere('stations:streamers:index'),
                        'permission' => StationPermissions::Streamers,
                    ],

                    'web_dj' => [
                        'label' => __('Web DJ'),
                        'icon' => 'surround_sound',
                        'url' => (string)(
                        $router->namedAsUri(
                            'public:dj',
                            ['station_id' => $station->getShortName()],
                            [],
                            true
                        )->withScheme('https')
                        ),
                        'visible' => $station->getEnablePublicPage(),
                        'external' => true,
                    ],
                ],
            ],

            'webhooks' => [
                'label' => __('Web Hooks'),
                'icon' => 'code',
                'url' => $router->fromHere('stations:webhooks:index'),
                'visible' => StationFeatures::Webhooks->supportedForStation($station),
                'permission' => StationPermissions::WebHooks,
            ],

            'reports' => [
                'label' => __('Reports'),
                'icon' => 'assignment',
                'permission' => StationPermissions::Reports,
                'items' => [
                    'reports_overview' => [
                        'label' => __('Station Statistics'),
                        'url' => $router->fromHere('stations:reports:overview'),
                    ],
                    'reports_listeners' => [
                        'label' => __('Listeners'),
                        'url' => $router->fromHere('stations:reports:listeners'),
                    ],
                    'reports_requests' => [
                        'label' => __('Song Requests'),
                        'url' => $router->fromHere('stations:reports:requests'),
                        'visible' => $station->getEnableRequests(),
                    ],
                    'reports_timeline' => [
                        'label' => __('Song Playback Timeline'),
                        'url' => $router->fromHere('stations:reports:timeline'),
                    ],
                    'reports_soundexchange' => [
                        'label' => __('SoundExchange Royalties'),
                        'url' => $router->fromHere('stations:reports:soundexchange'),
                    ],
                ],
            ],

            'broadcasting' => [
                'label' => __('Broadcasting'),
                'icon' => 'wifi_tethering',
                'items' => [
                    'mounts' => [
                        'label' => __('Mount Points'),
                        'icon' => 'wifi_tethering',
                        'url' => $router->fromHere('stations:mounts:index'),
                        'visible' => StationFeatures::MountPoints->supportedForStation($station),
                        'permission' => StationPermissions::MountPoints,
                    ],
                    'hls_streams' => [
                        'label' => __('HLS Streams'),
                        'url' => $router->fromHere('stations:hls_streams:index'),
                        'visible' => StationFeatures::HlsStreams->supportedForStation($station),
                        'permission' => StationPermissions::MountPoints,
                    ],
                    'remotes' => [
                        'label' => __('Remote Relays'),
                        'icon' => 'router',
                        'url' => $router->fromHere('stations:remotes:index'),
                        'visible' => StationFeatures::RemoteRelays->supportedForStation($station),
                        'permission' => StationPermissions::RemoteRelays,
                    ],
                    'fallback' => [
                        'label' => __('Custom Fallback File'),
                        'url' => $router->fromHere('stations:fallback'),
                        'visible' => StationFeatures::Media->supportedForStation($station),
                        'permission' => StationPermissions::Broadcasting,
                    ],
                    'ls_config' => [
                        'label' => __('Edit Liquidsoap Configuration'),
                        'url' => $router->fromHere('stations:util:ls_config'),
                        'visible' => StationFeatures::CustomLiquidsoapConfig->supportedForStation($station),
                        'permission' => StationPermissions::Broadcasting,
                    ],
                    'stations:stereo_tool_config' => [
                        'label' => __('Upload Stereo Tool Configuration'),
                        'url' => $router->fromHere('stations:stereo_tool_config'),
                        'visible' => $settings->getEnableAdvancedFeatures()
                            && StationFeatures::Media->supportedForStation($station)
                            && AudioProcessingMethods::StereoTool === $backendConfig->getAudioProcessingMethodEnum(),
                        'permission' => StationPermissions::Broadcasting,
                    ],
                    'queue' => [
                        'label' => __('Upcoming Song Queue'),
                        'url' => $router->fromHere('stations:queue:index'),
                        'permission' => StationPermissions::Broadcasting,
                        'visible' => $station->supportsAutoDjQueue(),
                    ],
                    'restart' => [
                        'label' => __('Restart Broadcasting'),
                        'class' => 'api-call',
                        'url' => $router->fromHere('stations:restart:index'),
                        'permission' => StationPermissions::Broadcasting,
                        'visible' => $hasLocalServices,
                    ],
                ],
            ],

            'logs' => [
                'label' => __('Logs'),
                'icon' => 'web_stories',
                'url' => $router->fromHere('stations:logs'),
                'permission' => StationPermissions::Logs,
            ],
        ]
    );
};
