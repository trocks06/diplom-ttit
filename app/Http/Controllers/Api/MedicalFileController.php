<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalFileRequest;
use App\Http\Requests\UpdateMedicalFileRequest;
use App\Http\Resources\MedicalFileResource;
use App\Models\MedicalFile;
use App\Models\MedicalRecord;
use App\Services\MedicalFileService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MedicalFileController extends Controller
{
    use AuthorizesRequests;

    protected MedicalFileService $service;

    public function __construct(MedicalFileService $service)
    {
        $this->service = $service;
    }

    public function store(StoreMedicalFileRequest $request, MedicalRecord $medicalRecord)
    {
        $this->authorize('create', [MedicalFile::class, $medicalRecord]);

        $file = $this->service->upload($request->file('file'), $medicalRecord);
        return new MedicalFileResource($file);
    }

    public function destroy(MedicalFile $medicalFile)
    {
        $this->authorize('delete', $medicalFile);
        $this->service->deleteFile($medicalFile);
        return response()->json(['message' => 'Файл удалён']);
    }

    public function update(UpdateMedicalFileRequest $request, MedicalFile $medicalFile)
    {
        $this->authorize('update', $medicalFile);

        $updatedFile = $this->service->updateFile($medicalFile, $request->file('file'), $request->input('file_name'));

        return new MedicalFileResource($updatedFile);
    }
}
