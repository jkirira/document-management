<?php


namespace App\Services;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Support\Str;

class DocumentService
{
    public $document;

    public function __construct(Document $document = null)
    {
        $this->document = $document;
    }

    public function saveFileToStorage($file, Folder $folder=null)
    {
        $path = config('constants.documents.storage_folder', '');

        if (isset($folder)) {
            $folderPathString = Str::slug($folder->name);
            $currentFolder = $folder;

            while(isset($currentFolder->parentFolder)) {
                $folderPathString = Str::slug($currentFolder->parentFolder->name) . '/' . $folderPathString;
                $currentFolder = $currentFolder->parentFolder;
            }

            $path =  '/' . $folderPathString;

        }

        return $file->store($path, 'public');

    }

    public function addNewDocument($file, $input, $owner = null)
    {
        $folder = isset($input['folder_id']) ? Folder::find($input['folder_id']) : null;

        $path = $this->saveFileToStorage($file, $folder);

        if (!isset($owner)) {
            $owner = auth()->user();
        }

        if ($path) {
            $document = Document::create([
                                    "name" => $input['name'],
//                                    "filename" => $file->extension(),
                                    "extension" => $file->extension(),
                                    "path" => $path,
                                    'added_by' => $owner->id,
                                    'folder_id' => $folder ? $folder->id : null,
                                ]);

            return $document;

        } else {
            throw \Exception::class('Could not add document');
        }

    }

}
