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
        $path = $file->storeAs('medical_files/' . $medicalRecord->id, $filename, 'local');
        return $medicalRecord->medical_files()->create([
            'file_name' => $originalName,
            'file_path' => $path,
            'file_type' => $extension,
        ]);
    }

    public function deleteFile(MedicalFile $file): void
    {
        if (Storage::disk('local')->exists($file->file_path)) {
            Storage::disk('local')->delete($file->file_path);
        }
        $file->delete();
    }

    public function replaceFile(MedicalFile $medicalFile, UploadedFile $newFile, ?string $customName = null): MedicalFile
    {
        if (Storage::disk('local')->exists($medicalFile->file_path)) {
            Storage::disk('local')->delete($medicalFile->file_path);
        }
        $originalName = $customName ?: $newFile->getClientOriginalName();
        $extension = $newFile->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;
        $path = $newFile->storeAs('medical_files/' . $medicalFile->medical_record_id, $filename, 'local');
        $medicalFile->update([
            'file_name' => $originalName,
            'file_path' => $path,
            'file_type' => $extension,
        ]);
        return $medicalFile;
    }

    public function updateFileName(MedicalFile $medicalFile, ?string $newName): MedicalFile
    {
        if ($newName) {
            $medicalFile->update(['file_name' => $newName]);
        }
        return $medicalFile;
    }
}
