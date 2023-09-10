<?php
namespace App\Transformers\Admin;

use App\Models\Folder;

class FolderTransformer
{
    public function transform(Folder $folder)
    {
        $parentFolder = $folder->parentFolder;
        $childFolders = $folder->childFolders;

        return [
            'id' => $folder->id,
            'name' => $folder->name,

            'parent_folder' => isset($parentFolder)
                            ? [
                                'id' => $parentFolder->id,
                                'name' => $parentFolder->name,
                            ]
                            : null,

            'child_folders' => count($childFolders)
                            ? $childFolders->map(function ($childFolder) {
                                        return [
                                            'id' => $childFolder->id,
                                            'name' => $childFolder->name,
                                        ];
                                    })
                            : [],
            'hasParentFolder' => isset($parentFolder),
            'hasChildFolders' => (bool)count($childFolders),
        ];
    }
}
