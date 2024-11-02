<?php

namespace App\EventListener;

use App\Entity\File;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class FileListener
{

    public function prePersist(File $file, LifecycleEventArgs $event): void
    {

        $file->setSendingDate(new \Datetime());
    }
}