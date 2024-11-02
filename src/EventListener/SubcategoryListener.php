<?php
namespace App\EventListener;
use App\Entity\Subcategory;
use Doctrine\Persistence\Event\LifecycleEventArgs;


class SubcategoryListener
{

    public function prePersist(Subcategory $subcategory, LifecycleEventArgs $event): void
    {
        $entityManager = $event->getObjectManager();
        $repository = $entityManager->getRepository(Subcategory::class);
        $count = $repository->findDuplicate($subcategory->getNumber(), $subcategory->getCategory());
        
        if ($count > 0) {
            throw new \RuntimeException('Le numéro est déjà utilisé.');
        }
    }

 }