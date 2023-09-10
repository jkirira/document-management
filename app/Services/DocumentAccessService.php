<?php


namespace App\Services;


use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentAccess;
use App\Models\Role;
use App\Models\User;

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

        foreach(DocumentAccess::ACCESS_ABILITIES as $accessType) {
            $value = isset($values[$accessType]) ? (bool)$values[$accessType] : false;
            $access->setAttribute($accessType, $value);
        }

        $grantedBy = isset($grantedBy) ? $grantedBy : auth()->user();
        $access->grantedBy()->associate($grantedBy);

        $access->save();
        //    `granted_by`,

        //    `access_request_id`,
        //]

        //    `expired`,
        //    `expires_at`,
        //    `revoked`,
        //    `revoked_at`,
        //    `revoked_by`,


    }

    public function updateAccess(DocumentAccess $access, $values)
    {
        $access->department_id = isset($values['department_id']) ? $values['department_id'] : $access->department_id;
        $access->all_departments = isset($values['all_departments']) ? $values['all_departments'] : $access->all_departments;

        $access->role_id = isset($values['role_id']) ? $values['role_id'] : $access->role_id;
        $access->all_roles = isset($values['all_roles']) ? $values['all_roles'] : $access->all_roles;

        foreach(DocumentAccess::ACCESS_ABILITIES as $accessType) {
            $value = isset($values[$accessType]) ? (bool)$values[$accessType] : false;
            $access->setAttribute($accessType, $value);
        }

        $access->save();

        return $access;

    }

    public function documentIsAccessibleByEveryone(Document $document, $ability=null)
    {
        return Document::accessibleToEveryone($ability)->where('id', $document->id)->exists();
    }

    public function documentIsAccessibleByDepartment(Document $document, Department $department, $ability=null)
    {
        return Document::accessibleToDepartment($department, $ability)->where('id', $document->id)->exists();
    }

    public function documentIsAccessibleByRole(Document $document, Role $role, $ability=null)
    {
        return Document::accessibleToRole($role, $ability)->where('id', $document->id)->exists();
    }

    public function documentIsAccessibleByRoles(Document $document, Array $roles, $ability=null)
    {
        return Document::accessibleToRoles($roles, $ability)->where('id', $document->id)->exists();
    }

    public function documentIsAccessibleByUser(Document $document, User $user, $ability=null)
    {
        return Document::accessibleToUser($user, $ability)->where('id', $document->id)->exists();
    }

    public function folderIsAccessibleByUser(Folder $folder, User $user)
    {
        return Folder::accessibleToUser($user, 'view')->where('id', $folder->id)->exists();
    }

    public function revokeAccess(DocumentAccess $access)
    {
        $access->revoked = true;
        $access->revokedBy()->associate(auth()->user());
        $access->save();

        $access->delete();

    }


}
