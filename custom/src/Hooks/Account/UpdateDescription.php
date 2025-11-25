<?php

namespace Espo\Custom\Hooks\Account;

use Espo\Core\Hook\Hook\BeforeSave;
use Espo\ORM\Entity;

class UpdateDescription implements BeforeSave
{
    public function beforeSave(Entity $entity, array $options): void
    {
        $entityManager = $entity->getEntityManager();
        $contactIds = [];

        // Check if contacts are being linked in this save operation
        if ($entity->has('contactsIds')) {
            $contactIds = $entity->get('contactsIds') ?? [];
        } else {
            // For existing entities, get current contacts from database
            if (!$entity->isNew()) {
                $existingEntity = $entityManager->getEntity('Account', $entity->getId());
                if ($existingEntity) {
                    $contactIds = $existingEntity->getLinkMultipleIdList('contacts');
                }
            }
        }
        
        if (empty($contactIds)) {
            return;
        }

        $contactList = [];

        foreach ($contactIds as $contactId) {
            $contact = $entityManager->getEntity('Contact', $contactId);
            
            if (!$contact) {
                continue;
            }

            $name = $contact->get('name') ?? 'N/A';
            $role = $contact->get('accountRole') ?? 'N/A';
            
            $contactList[] = "{$name} (Role: {$role})";
        }

        if (!empty($contactList)) {
            $description = "Related Contacts:\n" . implode("\n", $contactList);
            $entity->set('description', $description);
        }
    }
}

