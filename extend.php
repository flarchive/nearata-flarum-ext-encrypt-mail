<?php

namespace Nearata\EncryptMail;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\Extend;
use Flarum\User\User;
use Illuminate\Mail\Events\MessageSending;
use Nearata\EncryptMail\Api\Controller\UpdatePublicKeyController;
use Nearata\EncryptMail\Api\Serializer\UserSerializerAttributes;
use Nearata\EncryptMail\Filesystem\EncryptMailDisk;
use Nearata\EncryptMail\Listener\MessageSendingListener;
use Nearata\EncryptMail\Model\EncryptMailModel;
use Nearata\EncryptMail\Throttler\UpdatePublicKeyThrottler;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Filesystem)
        ->disk('nearataEncryptMail', EncryptMailDisk::class),

    (new Extend\Event)
        ->listen(MessageSending::class, MessageSendingListener::class),

    (new Extend\Model(User::class))
        ->hasOne('nearata_encrypt_mail', EncryptMailModel::class, 'id'),

    (new Extend\ApiSerializer(UserSerializer::class))
        ->attributes(UserSerializerAttributes::class),

    (new Extend\Routes('api'))
        ->post('/nearata/encryptMail/updatePublicKey', 'nearata.encrypt-mail.update-public-key', UpdatePublicKeyController::class),

    (new Extend\ThrottleApi)
        ->set('nearataEncryptMail', UpdatePublicKeyThrottler::class)
];
