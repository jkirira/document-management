<?php


namespace App\Services;


use App\Models\Document;
use App\Models\DocumentAccess;
use App\Models\Folder;
use App\Models\User;
use Carbon\Carbon;

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

        $access->user_id = isset($values['user_id']) ? $values['user_id'] : null;

        $access->expires_at = isset($values['expires_at']) ? $values['expires_at'] : null;

        foreach(DocumentAccess::ACCESS_ABILITIES as $accessType) {
            $value = isset($values[$accessType]) ? (bool)$values[$accessType] : false;
            $access->setAttribute($accessType, $value);
        }

        $grantedBy = isset($grantedBy) ? $grantedBy : auth()->user();
        $access->grantedBy()->associate($grantedBy);

        $access->save();

        return $access;

    }

    public function updateAccess(DocumentAccess $access, $values)
    {
        $access->department_id = isset($values['department_id']) ? $values['department_id'] : $access->department_id;
        $access->all_departments = isset($values['all_departments']) ? $values['all_departments'] : $access->all_departments;

        $access->role_id = isset($values['role_id']) ? $values['role_id'] : $access->role_id;
        $access->all_roles = isset($values['all_roles']) ? $values['all_roles'] : $access->all_roles;

//        $access->user_id = isset($values['user_id']) ? $values['user_id'] : $access->user_id;

        $access->expires_at = isset($values['expires_at']) ? $values['expires_at'] : $access->expires_at;

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

    public function documentIsAccessibleByUser(Document $document, User $user, $ability=null)
    {
        return Document::accessibleToUser($user, $ability)->where('id', $document->id)->exists();
    }

    public function folderIsAccessibleByUser(Folder $folder, User $user)
    {
        $foldersAccessibleByUser = $this->foldersAccessibleByUser($user);
        return $foldersAccessibleByUser->contains('id', $folder->id);
    }

    public function foldersAccessibleByUser(User $user, $tree=false)
    {
        $folders = Folder::where(function($query) use ($user) {
                            $query->accessibleToUser($user, 'view');
                        })
                        ->get();

        $parentFolders = [];
        foreach($folders as $folder) {
            $parent = $folder->parentFolder;
            while(isset($parent)) {
                $parentFolders[] = $parent;
                $parent = $parent->parentFolder;
            }
        }

        $folders = collect($parentFolders)->merge($folders)->unique('id');

        if ($tree) {
            $folders = $folders->keyBy('id');

            $folderWithLargestParentId = $this->findFolderWithLargestParentId($folders);

            while ($folderWithLargestParentId) {
                $parent = $folders[$folderWithLargestParentId->parent_id];

                $parent['children'] = isset($parent['children'])
                                        ? array_merge($parent['children'], [$folderWithLargestParentId])
                                        : [ $folderWithLargestParentId ];

                unset($folders[$folderWithLargestParentId->id]);

                $folderWithLargestParentId = $this->findFolderWithLargestParentId($folders);

            }

        }

        return $folders;
    }

    private function findFolderWithLargestParentId($folders=[]) {
        $folderWithLargestParentId = null;

        foreach ($folders as $folder) {
            if (
                isset($folder->parent_id)
                &&
                (!$folderWithLargestParentId || $folderWithLargestParentId->parent_id < $folder->parent_id))
            {
                $folderWithLargestParentId = $folder;
            }
        }

        return $folderWithLargestParentId;
    }

    public function revokeAccess(DocumentAccess $access)
    {
        $access->revoked = true;
        $access->revokedBy()->associate(auth()->user());
        $access->revoked_at = Carbon::now();
        $access->save();

        return $access;

    }

}
