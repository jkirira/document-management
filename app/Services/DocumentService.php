<?php


namespace App\Services;

use App\Models\Document;
use App\Models\DocumentAccess;
use App\Models\Folder;
use App\Models\UserCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    public $document;

    public function __construct(Document $document = null)
    {
        $this->document = $document;
    }

    public function addNewDocument($file, $input, $owner = null)
    {
        return DB::transaction(function () use ($file, $input, $owner) {

            $folder = isset($input['folder_id']) ? Folder::find($input['folder_id']) : null;
            $storage_location = isset($folder) ? $this->generateStorageLocation($folder) : config('constants.documents.storage_folder', '');
            $path = $file->store($storage_location, 'public');
            if (!$path) {
                throw \Exception::class('Could not add document');
            }

            $document = Document::create([
                "name" => $input['name'],
    //            "filename" => $file->extension(),
                "extension" => $file->extension(),
                "path" => $path,
                'added_by' => isset($owner) ? $owner->id : auth()->user()->id,
                'folder_id' => $folder ? $folder->id : null,
            ]);


            // Grant user document access
            $accessDetails = [];
            $accessDetails['user_id'] = auth()->user()->id;
            foreach (DocumentAccess::ACCESS_ABILITIES as $ability) {
                $accessDetails[$ability] = true;
            }
            (new DocumentAccessService())->grantAccess($document, $accessDetails);


            // Add user to access managers
            $document->accessManagers()->attach(auth()->user()->id);

            // Add document categories
            if (isset($input['category_ids'])) {
                $categories = UserCategory::whereIn('id', $input['category_ids'])->get();
                $document->categories()->sync($categories->pluck('id'));
            }

            return $document;
        });

    }

    public function updateDocument(Document $document, $input)
    {
        return DB::transaction(function () use ($document, $input) {

            $folder = isset($input['folder_id']) ? Folder::find($input['folder_id']) : null;
            if(isset($folder) && ($folder->id !== $document->folder_id)) {
                // move document
                $storage_location = $this->generateStorageLocation($folder);
                Storage::move($document->path, $storage_location);

                $document->path = $storage_location;
                $document->folder_id = $folder->id;
            }

            $document->name = $input['name'];
    //        $document->filename = $file->extension();
    //        $document->extension = $file->extension();
            $document->save();

            if (isset($input['category_ids'])) {
                $categories = UserCategory::whereIn('id', $input['category_ids'])->get();
                $document->categories()->sync($categories->pluck('id'));
            }

            return $document;
        });
    }

    public function generateStorageLocation(Folder $folder) {
        $storage_location = null;

        $folder_path_string = Str::slug($folder->name);
        $currentFolder = $folder;

        while(isset($currentFolder->parentFolder)) {
            $folder_path_string = Str::slug($currentFolder->parentFolder->name) . '/' . $folder_path_string;
            $currentFolder = $currentFolder->parentFolder;
        }

        $storage_folder_location = config('constants.documents.storage_folder', '');
        $storage_location = $storage_folder_location ? $storage_folder_location . '/' . $folder_path_string : $folder_path_string;

        return $storage_location;
    }

}
