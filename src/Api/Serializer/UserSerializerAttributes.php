<?php

namespace Nearata\EncryptMail\Api\Serializer;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\User;

class UserSerializerAttributes
{
    public function __invoke(UserSerializer $serializer, User $model, array $attributes)
    {
        return [
            'nearataEncryptMailPublicKey' => $model->nearata_encrypt_mail->public_key
        ];
    }
}
