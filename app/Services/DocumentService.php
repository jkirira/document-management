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

    public function saveFileToStorage($file, $location=null)
    {
        $storage_location = $location ?? config('constants.documents.storage_folder', '');
        return $file->store($storage_location, 'public');
    }

    public function addNewDocument($file, $input, $owner = null)
    {
        $folder = isset($input['folder_id']) ? Folder::find($input['folder_id']) : null;

        $storage_location = null;

        if (isset($folder)) {
            $folder_path_string = Str::slug($folder->name);
            $currentFolder = $folder;

            while(isset($currentFolder->parentFolder)) {
                $folder_path_string = Str::slug($currentFolder->parentFolder->name) . '/' . $folder_path_string;
                $currentFolder = $currentFolder->parentFolder;
            }

            $storage_folder_location = config('constants.documents.storage_folder', '');
            $storage_location = $storage_folder_location ? $storage_folder_location . '/' . $folder_path_string : $folder_path_string;

        }

        $path = $this->saveFileToStorage($file, $storage_location);

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
