<?php
use App\Enums\ProcurementProcess;
use Illuminate\Http\UploadedFile;

if (!function_exists('SaveDocument')) {
    function SaveDocument(UploadedFile $file, ProcurementProcess $process, int $procurement_id, string $filename) : bool|string
    {
        $getFileExt   = $file->getClientOriginalExtension();
        $folder       = "uploads/documents/$procurement_id/" . $process->name;

        $filepath     =  $file->storeAs($folder, "{$filename}.{$getFileExt}", ['disk' => 'public']);
        return $filepath;
    }
}
