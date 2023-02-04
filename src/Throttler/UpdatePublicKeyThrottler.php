<?php

namespace Nearata\EncryptMail\Throttler;

use Carbon\Carbon;
use Flarum\Http\RequestUtil;
use Psr\Http\Message\ServerRequestInterface;

class UpdatePublicKeyThrottler
{
    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getAttribute('routeName') !== 'nearata.encrypt-mail.update-public-key') {
            return;
        }

        $actor = RequestUtil::getActor($request);

        /** @var ?\Nearata\EncryptMail\Model\EncryptMailModel */
        $enc = $actor->nearata_encrypt_mail;

        if (is_null($enc)) {
            return false;
        }

        if (Carbon::now()->lt($enc->updated_at->addMinutes(5))) {
            return true;
        }

        return false;
    }
}
