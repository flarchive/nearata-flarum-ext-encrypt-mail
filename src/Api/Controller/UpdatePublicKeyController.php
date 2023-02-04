<?php

namespace Nearata\EncryptMail\Api\Controller;

use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\EmptyResponse;
use Nearata\EncryptMail\Model\EncryptMailModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UpdatePublicKeyController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);

        $actor->assertRegistered();

        $value = Arr::get($request->getParsedBody(), 'publicKey');

        if (is_null($value)) {
            throw new BadRequestException;
        }

        $enc = EncryptMailModel::findByEmailOrCreate($actor->email);

        if ($enc->public_key == $value) {
            throw new BadRequestException;
        }

        $enc->public_key = $value;
        $enc->imported = false;
        $enc->save();

        return new EmptyResponse;
    }
}
