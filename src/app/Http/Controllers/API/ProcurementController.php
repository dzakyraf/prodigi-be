<?php

namespace App\Http\Controllers\API;

use App\DataClass\NewProcFormData;
use App\DataClass\ProcurementDocsData;
use App\DataClass\ProcurementHistory;
use App\Enums\ApprovalStatus;
use App\Enums\DataStatus;
use App\Enums\ProcurementProcess;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\ListDocumentMasterAPIRequest;
use App\Http\Requests\API\NewProposalAPIRequest;
use App\Mail\NewProposalEmail;
use App\Models\ProcurementDocumentMaster;
use App\Models\ProposalModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Request;

class ProcurementController extends AppBaseController
{

    private ProposalModel $proposalModel;

    public function __construct()
    {
        $this->proposalModel = new ProposalModel();
    }



    public function getDocumentList(ListDocumentMasterAPIRequest $request): JsonResponse
    {
        $input = $request->all();
        $documents = ProcurementDocumentMaster::where('process_id', $input['process_id'])
            ->where('data_status', 'active');


        if ($request->has('procurement_id') && $input['procurement_id'] != '') {
            $documents = $documents
                ->leftJoin('prod_procurement_document', 'prod_procurement_document.document_ms_id', '=', 'prod_procurement_document_list_ms.procurement_document_list_id')
                ->where('prod_procurement_document.procurement_id', $input['procurement_id'])
                ->select('prod_procurement_document.document_title as existing_document')
                ->groupBy('prod_procurement_document.procurement_document_id');
        }
        $documents =
            $documents->orderBy('seq')
            ->groupByRaw('procurement_document_list_id')
            ->select('prod_procurement_document_list_ms.*')
            ->get();
        return $this->sendResponse($documents, 'Get Document List Successfully.');
    }


    public function download(Request $request)
    {
        $input = $request->all();
        $pdf = Pdf::loadView('email.notification',[
            'status' => 'info',
            'procurement_title' => "HELLO",
            'nodin' => "NODIN",
            'title' => 'Pengajuan Berhasil Dibuat',
            'messages' => 'Pengajuan Anda Sudah diteruskan untuk di approval',
        ]);
        return $pdf->download('invoice.pdf');
        // return $this->sendResponse($documents, 'Get Document List Successfully.');
    }




    public function newProposal(NewProposalAPIRequest $request): JsonResponse
    {

        $input = $request->all();
        $docs  = [];

        $files = $request->allFiles();
        $file_ms  = $this->proposalModel->getDocuments(1);
        // $mapping_file = array(
        //     'justifikasi_file' => 'dokumen_justifikasi',
        //     'rab_file' => 'dokumen_rab',
        //     'nodin_file' => 'dokumen_nodin',
        //     'ba_file' => 'dokumen_ba',
        //     'tor_file' => 'dokumen_tor',
        // );

        $data =   NewProcFormData::from([
            'name' => $input['name'],
            'nodin' => $input['nodin'],
            'division_id' => $input['division_id'],
            'unit_mpp_id' => $input['unit_mpp_id'],
            'nodin_date' => $input['nodin_date'],
            'rab_amount' => $input['rab_amount'],
            'last_process_id' => ProcurementProcess::PERMOHONAN->value,
            'last_status' => ApprovalStatus::NeedReview->value,
            'creator_id' => 1,
        ]);

        $procurement_id =        $this->proposalModel->addProposal($data);



        foreach ($files as  $key => $uploadedFile) {
            $filename =    $file_ms->firstWhere('document_code', $key)['document_title'];
            $uploadedFile = SaveDocument($request->file($key), ProcurementProcess::PERMOHONAN, $procurement_id, $filename);

            if ($uploadedFile) {
                $proc_docs = ProcurementDocsData::from([
                    'procurement_process_id' => ProcurementProcess::PERMOHONAN->value,
                    'document_type' => $file_ms->firstWhere('document_code', $key)['filetype'],
                    'value' => $uploadedFile,
                    'document_ms_id' => $file_ms->firstWhere('document_code', $key)['procurement_document_list_id'],
                    'data_status' => DataStatus::Active->value,
                    'procurement_id' => $procurement_id,
                ]);

                $docs[] = $proc_docs;
            }
        }
        $this->proposalModel->addDocument($docs);

        $history =  ProcurementHistory::from([
            'procurement_id' => $procurement_id,
            'procurement_process_id' => ProcurementProcess::PERMOHONAN->value,
            'status' => ApprovalStatus::NeedReview->value,
            'user_id' => Auth::id(),
            'remark' => 'Pengajuan Dibuat',
        ]);

        $this->proposalModel->addHistory($history);

        $this->proposalModel->createApprovalList($procurement_id, $input['division_id']);


        Mail::to("dzakylmg87@gmail.com")->send(new NewProposalEmail([
            'status' => 'info',
            'procurement_title' => $input['name'],
            'nodin' => $input['nodin'],
            'title' => 'Pengajuan Berhasil Dibuat',
            'messages' => 'Pengajuan Anda Sudah diteruskan untuk di approval',
        ]));





        // Mail::to("approver1@gmail.com")->send(new NotificationEmail([
        //     'status' => 'info',
        //     'procurement_title' => $input['name'],
        //     'nodin' => $input['nodin'],
        //     'title' => 'Pengajuan Butuh Persetujuan',
        //     'messages' => 'Terdapat Pengajuan yang membutuhkan persetujuan anda. Silahkan cek ke prodigi untuk melakukan approval',
        // ]));

        // Telegram::sendMessage([
        //     'chat_id' => '94867628',
        //     'text' => 'Pengajuan Baru berhasil dibuat, sedang diteruskan untuk di Approval ' . PHP_EOL . ' Pengajuan : ' . $input['name'] . '' . PHP_EOL . ' Nodin : ' . $input['nodin'] . PHP_EOL . ' Requestor : Admin 1'
        // ]);

        // Log::build([
        //     'driver' => 'single',
        //     'path' => storage_path('logs/custom.log'),
        // ])->info(json_encode($docs));


        return $this->sendSuccess('Position deleted successfully');
    }

    public function listProposal(Request $request): JsonResponse
    {
        // $input = $request->all();
        $procurement = DB::table('prod_procurement as pp')
            ->leftJoin('prod_app_unit_mpp_ms as pmp', 'pmp.unit_mpp_id', '=', DB::raw('ANY(pp.unit_mpp_id)'))
            ->leftJoin('prod_app_division_ms as ppd', 'pp.division_id', '=', 'ppd.division_id')
            ->leftJoin('prod_procurement_process_ms as pmpp', 'pp.last_process_id', '=', 'pmpp.procurement_process_id')
            // ->where('usa.status', '=', 'active')
            // ->where('usa.user_id',  '=', $user_id)
            ->groupByRaw('pp.procurement_id,ppd.division_name,pmpp.process_name')
            ->selectRaw("
            pp.*,
            pmpp.process_name as last_process,
            STRING_AGG ( pmp.mpp_name :: TEXT, ', ' ORDER BY pmp.mpp_name ) AS unit_names,
            ppd.division_name")
            ->get();


        return $this->sendResponse($procurement, 'Get Document List Successfully.');
    }


    public function detailsProposal(Request $request): JsonResponse
    {
        $user   = Auth::user();
        $input  = Request::all();
        $procurement_id = $input['procurement_id'];
        $procurement = DB::table('prod_procurement as pp')
            ->leftJoin('prod_app_unit_mpp_ms as pmp', 'pmp.unit_mpp_id', '=', DB::raw('ANY(pp.unit_mpp_id)'))
            ->leftJoin('prod_app_division_ms as ppd', 'pp.division_id', '=', 'ppd.division_id')
            ->leftJoin('prod_procurement_process_ms as pms', 'pp.last_process_id', '=', 'pms.procurement_process_id')
            ->leftJoin('prod_procurement_proc_rendan as pmpd', 'pp.procurement_id', '=', 'pmpd.procurement_id')
            ->leftJoin('prod_procurement_proc_lakdan as pmpl', 'pp.procurement_id', '=', 'pmpl.procurement_id')
            ->leftJoin('prod_procurement_proc_penyelesaian as pmpld', 'pp.procurement_id', '=', 'pmpld.procurement_id')
            // ->where('usa.status', '=', 'active')
            ->where('pp.procurement_id',  '=', $procurement_id)
            ->groupByRaw('pp.procurement_id,pms.process_name,ppd.division_name')
            ->selectRaw("
                        pp.*,
                        STRING_AGG ( pmp.mpp_name :: TEXT, ', ' ORDER BY pmp.mpp_name ) AS unit_names,
                        ppd.division_name,
                        pms.process_name")
            ->get()
            ->first();


        $document = $this->proposalModel->getDocuments(null, $procurement_id);
        $history = $this->proposalModel->getProcurementHistory($procurement_id);


        $procurement->document = $document;
        $procurement->history  = $history;

        $approve = DB::table('prod_procurement_approval_list as pp')
            ->where('pp.procurement_id',  '=', $input['procurement_id'])
            ->where('pp.approval_status',  '=', ApprovalStatus::NeedReview->value)
            ->where('pp.position_id', "=" ,$user->position_id)
            ->selectRaw("*")
            ->get();

        $procurement->canApprove = $approve->count() > 0 ? true : false;

        return $this->sendResponse($procurement, 'Get Document List Successfully.');
    }

    public function reviewProposal(Request $request): JsonResponse
    {
        try {
            $input = Request::all();
            $status                  = $input['approval_status'];
            $procurement_id          = $input['procurement_id'];
            $process_id              = $input['processs_id'];

            $approval_list = DB::table('prod_procurement_approval_list as pp')
                ->where('pp.procurement_id',  '=', $procurement_id)
                ->where('position_id', Auth::user()->position_id)
                ->selectRaw("*")
                ->get()
                ->first();

            DB::table('prod_procurement_approval_list')
                ->where('procurement_id', $procurement_id)
                ->where('position_id', Auth::user()->position_id)
                ->update([
                    'approval_status' => $status,
                    'notes' => $input['notes'],
                    'last_updated' => date('Y-m-d H:i:s'),
                    'action_by' => Auth::user()->user_id,
                    'approval_date' => $status == 'approved' ? date('Y-m-d H:i:s') : null
                ]);

            $lr = DB::table('prod_procurement_approval_list as pp')
                ->where('pp.procurement_id',  '=', $procurement_id)
                ->selectRaw(" COUNT ( CASE WHEN approval_status = 'approved' THEN 1 ELSE NULL END ) AS count_approved, MAX ( sequences )  as max_approved")
                ->get()
                ->first();

            // var_dump($lr);
            // var_dump($approval_list);

            if ($status == ApprovalStatus::Approved->value) {
                // Jika Jumlah Approval Belum Selesai
                if ($approval_list->sequences < $lr->max_approved) {
                    DB::table('prod_procurement_approval_list')
                        ->where('procurement_id', $procurement_id)
                        ->where('sequences', $approval_list->sequences + 1)
                        ->update([
                            'approval_status' => ApprovalStatus::NeedReview->value
                        ]);

                    $status = ApprovalStatus::NeedReview->value;
                    DB::table('prod_procurement')
                        ->where('procurement_id', $procurement_id)
                        ->update(
                            [
                            'last_status' => $status,
                            'last_process_id' => $process_id,
                            'status_notes' => $input['notes']
                            ]);
                }

                // Jika Jumlah Approval Selesai
                if ($lr->count_approved == $lr->max_approved) {
                    DB::table('prod_procurement')
                        ->where('procurement_id', $procurement_id)
                        ->update(
                            [
                                'last_status' => ApprovalStatus::Approved->value,
                                'last_process_id' => $process_id,
                                'status_notes' => $input['notes']]);

                }
            } else {
                DB::table('prod_procurement')
                    ->where('procurement_id', $procurement_id)
                    ->update(['last_status' => $status,'last_process_id' => $process_id,
                    'status_notes' => $input['notes']]);
            }

            $history =  ProcurementHistory::from([
                'procurement_id' => $procurement_id,
                'procurement_process_id' => $process_id,
                'status' => $status,
                'user_id' => Auth::id(),
                'remark' => 'Mengubah Status Pengajuan ke '.ApprovalStatus::from($input['approval_status'])->name(),
            ]);

            $this->proposalModel->addHistory($history);
            return $this->sendSuccess('Succesfully updated');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    public function historyProcurement(Request $request): JsonResponse
    {
        $input = $request->all();
        $procurement = DB::table('prod_procurement_history as pp')
            ->leftJoin('prod_users as pu', 'pu.user_id', '=', 'pp.user_id')
            ->leftJoin('prod_procurement_process_ms as ppd', 'ppd.procurement_process_id', '=', 'pp.procurement_process_id')
            ->where('pp.procurement_id', '=', $input['procurement_id'])
            // ->where('usa.user_id',  '=', $user_id)
            ->groupByRaw('pp.procurement_id,ppd.division_name')
            ->selectRaw("*")
            ->get();


        return $this->sendResponse($procurement, 'Get Document List Successfully.');
    }
}
