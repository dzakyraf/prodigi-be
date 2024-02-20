<?php

namespace App\Http\Controllers\API;


use App\DataClass\ProcurementDocsData;
use App\DataClass\ProcurementHistory;
use App\Enums\ApprovalStatus;
use App\Enums\DataStatus;
use App\Enums\ProcurementProcess;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\SaveDraftRendanAPIRequest;
use App\Mail\NewProposalEmail;
use App\Models\ProposalModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RendanController extends AppBaseController
{

    private ProposalModel $proposalModel;

    public function __construct()
    {
        $this->proposalModel = new ProposalModel();
    }




    public function saveDraft(SaveDraftRendanAPIRequest $request): JsonResponse
    {

        $input = $request->all();
        $docs  = [];

        $procurement_id = $input['procurement_id'];
        $files = $request->allFiles();
        $file_ms  = $this->proposalModel->getDocuments(2, $procurement_id);

        foreach ($file_ms as $doc) {
            $value = null;
            if ($doc['data_type'] == 'document') {
                if ($request->hasFile($doc['document_code'])) {
                    $upload = SaveDocument($request->file($doc['document_code']), ProcurementProcess::DOKUMEN_RENDAN, $procurement_id, $doc['document_title']);
                    if ($upload) $value = $upload;
                }
            } else {
                if (isset($input[$doc['document_code']])) $value = $input[$doc['document_code']];
            }

            if ($value != null) {
                $form  = ProcurementDocsData::from([
                    'procurement_process_id' => ProcurementProcess::DOKUMEN_RENDAN->value,
                    'document_type' => $doc['filetype'],
                    'value' => $value,
                    'document_ms_id' => $doc['procurement_document_list_id'],
                    'data_status' => DataStatus::Active->value,
                    'procurement_id' => $procurement_id,
                ]);


                if ($doc['key'] != null) {
                    $this->proposalModel->updateDocument($form, $doc['key']);
                } else {
                    $this->proposalModel->addDocument([$form->toArray()]);
                }
            }
        }

        $history =  ProcurementHistory::from([
            'procurement_id' => $procurement_id,
            'procurement_process_id' => ProcurementProcess::DOKUMEN_RENDAN->value,
            'status' => ApprovalStatus::OnDraft->value,
            'user_id' => Auth::id(),
            'remark' => 'Mengupload Dokumen',
        ]);

        $this->proposalModel->addHistory($history);

        return $this->sendSuccess('Draft Success!');
    }

    public function submit(SaveDraftRendanAPIRequest $request): JsonResponse
    {

        $input = $request->all();
        $docs  = [];

        $procurement_id = $input['procurement_id'];
        $files = $request->allFiles();
        $file_ms  = $this->proposalModel->getDocuments(2, $procurement_id);

        foreach ($file_ms as $doc) {
            if ($doc['value'] == null) {
                return $this->sendError('Dokumen Belum Lengkap!');
            }
        }

        DB::table('prod_procurement')
            ->where('procurement_id', $procurement_id)
            ->update(['last_status' => ApprovalStatus::OnDraft->value, 'last_process_id' => ProcurementProcess::DOKUMEN_LAKDAN->value,]);


        $history =  ProcurementHistory::from([
            'procurement_id' => $procurement_id,
            'procurement_process_id' => ProcurementProcess::DOKUMEN_RENDAN->value,
            'status' => ApprovalStatus::Approved->value,
            'user_id' => Auth::id(),
            'remark' => 'Menyelesaikan Dokumen',
        ]);

        $this->proposalModel->addHistory($history);

        return $this->sendSuccess('Draft Success!');
    }
}
