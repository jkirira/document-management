<?php
namespace App\Transformers\Client;

use App\Models\Folder;

class FolderTransformer
{
    public function transform(Folder $folder, $withChildren=false, $withParent=false)
    {
        $transformed = [
            'id' => $folder->id,
            'name' => $folder->name,
            'parent_id' => $folder->parent_id,
        ];

        if($withChildren) {
            $childFolders = $folder->childFolders;
            $transformed['child_folders'] = $childFolders->map(function ($childFolder) {
                                                return $this->transform($childFolder);
                                            });
        }

        if($withParent) {
            $parentFolder = $folder->parentFolder;
            $transformed['parent_folder'] = isset($parentFolder) ? $this->transform($parentFolder) : null;
        }

        return $transformed;
    }

    public function treeFromRoot(Folder $folder, $filterFunction=null)
    {
        $transformed = $this->transform($folder);

        $childFolders = $folder->childFolders;
        if($filterFunction) {
            $childFolders = $childFolders->filter($filterFunction);
        }

        $transformed['child_folders'] = $childFolders->map(function ($childFolder) use ($filterFunction) {
                                        return $this->treeFromRoot($childFolder, $filterFunction);
                                    })
                                    ->all();

        return $transformed;
    }

}
