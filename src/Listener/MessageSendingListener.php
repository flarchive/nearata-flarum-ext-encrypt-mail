<?php

namespace Nearata\EncryptMail\Listener;

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Str;
use Nearata\EncryptMail\Model\EncryptMailModel;

class MessageSendingListener
{
    /** @var \Illuminate\Filesystem\FilesystemAdapter */
    protected $filesystem;

    public function __construct(FilesystemFactory $factory)
    {
        $this->filesystem = $factory->disk('nearataEncryptMail');
    }

    public function handle(MessageSending $event)
    {
        $enc = $this->encrypt($event->message->getBody(), $event->message->getTo());

        $event->message->setBody($enc);
    }

    private function encrypt(string $body, array $addresses)
    {
        // Flarum doesn't sends email to multiple users at once
        if (count($addresses) > 1) {
            return $body;
        }

        $email = array_keys($addresses)[0];

        if (!$this->import($email)) {
            return $body;
        }

        $filename = Str::random(20) . '.txt';

        $this->filesystem->put($filename, $body);

        $path = $this->filesystem->path($filename);

        // --trust-model always
        exec("gpg --always-trust -e -a -r $email $path", $_, $resultCode);

        $this->filesystem->delete($filename);

        if ($resultCode != 0) {
            return $body;
        }

        $enc = $this->filesystem->get("$filename.asc");

        $this->filesystem->delete("$filename.asc");

        return $enc;
    }

    private function import(string $email): bool
    {
        $r = EncryptMailModel::findByEmailOrCreate($email);

        if (is_null($r)) {
            return false;
        }

        if ($r->imported) {
            return true;
        }

        if (is_null($r->public_key) || empty($r->public_key)) {
            exec("echo Hello, World! | gpg --always-trust -e -a -r $email", $_, $returnCode);

            if ($returnCode == 0) {
                $r->imported = true;
                $r->save();

                return true;
            }
        } else {
            $filename = Str::random(20) . '.txt';

            $this->filesystem->put($filename, $r->public_key);

            $path = $this->filesystem->path($filename);

            exec("gpg --import $path", $_, $returnCode);

            $this->filesystem->delete($path);

            if ($returnCode == 0) {
                $r->imported = true;
                $r->save();

                return true;
            }
        }

        if ($r->isDirty()) {
            $r->save();
        }

        return false;
    }
}
