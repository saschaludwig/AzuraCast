<?php

declare(strict_types=1);

namespace App\Controller\Stations;

use App\Controller\SingleActionInterface;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

final class QueueAction implements SingleActionInterface
{
    public function __invoke(
        ServerRequest $request,
        Response $response,
        array $params
    ): ResponseInterface {
        $router = $request->getRouter();

        return $request->getView()->renderVuePage(
            response: $response,
            component: 'Stations/Queue',
            id: 'station-queue',
            title: __('Upcoming Song Queue'),
            props: [
                'listUrl' => $router->fromHere('api:stations:queue'),
                'clearUrl' => $router->fromHere('api:stations:queue:clear'),
            ],
        );
    }
}
