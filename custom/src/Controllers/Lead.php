<?php

namespace Espo\Custom\Controllers;

use Espo\Core\Controllers\Record;
use Espo\Core\Api\Request;

class Lead extends Record
{
    public function postActionFindContacts(Request $request): object
    {
        $data = $request->getParsedBody();
        $id = $data->id ?? null;

        if (!$id) {
            return (object) [
                'success' => false,
                'message' => 'Lead ID is missing.'
            ];
        }

        $lead = $this->getEntityManager()->getEntity('Lead', $id);

        if (!$lead) {
            return (object) [
                'success' => false,
                'message' => 'Lead not found.'
            ];
        }

        $emailAddress = $lead->get('emailAddress');

        if (!$emailAddress) {
            return (object) [
                'success' => true,
                'message' => 'No email address found on this Lead.',
                'contacts' => []
            ];
        }

        $contacts = $this->getEntityManager()
            ->getRepository('Contact')
            ->where([
                'emailAddress' => $emailAddress
            ])
            ->find();

        $contactList = [];
        foreach ($contacts as $contact) {
            $contactList[] = [
                'id' => $contact->getId(),
                'name' => $contact->get('name'),
                'emailAddress' => $contact->get('emailAddress')
            ];
        }

        $count = count($contactList);
        $message = $count > 0 
            ? "Found {$count} contact(s) with the same email address:"
            : "No contacts found with the same email address.";

        return (object) [
            'success' => true,
            'message' => $message,
            'contacts' => $contactList
        ];
    }
}

