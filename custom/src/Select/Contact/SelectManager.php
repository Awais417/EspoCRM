<?php

namespace Espo\Custom\Select\Contact;

use Espo\Core\Select\SelectManager as BaseSelectManager;

class SelectManager extends BaseSelectManager
{
    protected function applyBoolFilterHasPhoneNumber(array $result): array
    {
        $result['whereClause'][] = [
            'OR' => [
                ['phone' => '!=', null],
                ['mobile' => '!=', null],
                ['phoneNumber' => '!=', null]
            ]
        ];

        return $result;
    }
}
