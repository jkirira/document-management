<?php


namespace App\Services;

use App\Models\Document;
use App\Models\DocumentAccess;

class DocumentAccessService
{

    public function __construct()
    {

    }

    public function grantAccess(Document $document, $values, $grantedBy=null)
    {
        $access = new DocumentAccess();

        $access->document_id = $document->id;

        $access->department_id = isset($values['department_id']) ? $values['department_id'] : null;
        $access->all_departments = isset($values['all_departments']) ? $values['all_departments'] : null;

        $access->role_id = isset($values['role_id']) ? $values['role_id'] : null;
        $access->all_roles = isset($values['all_roles']) ? $values['all_roles'] : null;

        foreach(DocumentAccess::ACCESS_TYPES as $accessType) {
            $value = isset($values[$accessType]) ? (bool)$accessType : false;
            $access->setAttribute($accessType, $value);
        }

        $grantedBy = isset($grantedBy) ? $grantedBy : auth()->user();
        $access->grantedBy()->associate($grantedBy);

        $access->save();

    }

}
