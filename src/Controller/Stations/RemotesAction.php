<?php

declare(strict_types=1);

namespace App\Controller\Stations;

use App\Controller\SingleActionInterface;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

final class RemotesAction implements SingleActionInterface
{
    public function __invoke(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $router = $request->getRouter();

        return $request->getView()->renderVuePage(
            response: $response,
            component: 'Stations/Remotes',
            id: 'station-remotes',
            title: __('Remote Relays'),
            props: [
                'listUrl' => $router->fromHere('api:stations:remotes'),
                'restartStatusUrl' => $router->fromHere('api:stations:restart-status'),
            ],
        );
    }
}
