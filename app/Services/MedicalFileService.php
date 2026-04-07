<?php

namespace App\Services;

use App\Models\MedicalFile;
use App\Models\MedicalRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MedicalFileService extends BaseService
{
    public function __construct(MedicalFile $model)
    {
        parent::__construct($model);
    }

    public function upload(UploadedFile $file, MedicalRecord $medicalRecord): MedicalFile
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;
        $path = $file->storeAs('medical_files/' . $medicalRecord->id, $filename, 'public');

        return $medicalRecord->medical_files()->create([
            'file_name' => $originalName,
            'file_path' => $path,
            'file_type' => $extension,
        ]);
    }

    public function delete(MedicalFile $file): void
    {
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();
    }
}
