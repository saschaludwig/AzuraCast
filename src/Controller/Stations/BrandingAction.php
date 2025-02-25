<?php

declare(strict_types=1);

namespace App\Controller\Stations;

use App\Assets\AssetTypes;
use App\Controller\SingleActionInterface;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

final class BrandingAction implements SingleActionInterface
{
    public function __invoke(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $router = $request->getRouter();

        return $request->getView()->renderVuePage(
            response: $response,
            component: 'Stations/Branding',
            id: 'stations-branding',
            title: __('Custom Branding'),
            props: [
                'profileEditUrl' => $router->fromHere(
                    'api:stations:profile:edit',
                ),
                'backgroundApiUrl' => $router->fromHere('api:stations:custom_assets', [
                    'type' => AssetTypes::Background->value,
                ]),
                'albumArtApiUrl' => $router->fromHere('api:stations:custom_assets', [
                    'type' => AssetTypes::AlbumArt->value,
                ]),
            ],
        );
    }
}
