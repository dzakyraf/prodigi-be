<?php

namespace App\Models;

use App\DataClass\NewProcFormData;
use App\DataClass\ProcurementDocsData;
use App\DataClass\ProcurementHistory;
use App\Enums\ApprovalStatus;
use Exception;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;



class ProposalModel
{
    public function addProposal(NewProcFormData $data): int
    {
        DB::table('prod_procurement')->insert(
            $data->toArray()
        );
        return DB::getPdo()->lastInsertId();
    }

    public function addDocument(array $data)
    {
        $_data = ProcurementDocsData::collection($data);
        foreach ($_data as $docs) {
            DB::table('prod_procurement_document')->insert(
                $docs->toArray()
            );
        }
    }

    public function updateDocument(ProcurementDocsData $data, int $procurement_doc_id)
    {
        DB::table('prod_procurement_document')->where('procurement_document_id', $procurement_doc_id)->update($data->toArray());
    }

    public function addHistory(ProcurementHistory $data): bool
    {
        // $_data = ProcurementDocsData::collection($data);

        DB::table('prod_procurement_history')->insert(
            $data->toArray()
        );
        return true;
    }

    public function getDocuments($process_id = null, $proc_id = null): Collection
    {
        $documents = ProcurementDocumentMaster::where('data_status', 'active');

        if ($process_id != null) {
            $documents = $documents->where('process_id', $process_id);
        }


        if ($proc_id != null) {
            $documents = $documents
                ->leftJoin('prod_procurement_document', function (JoinClause $join) use ($proc_id) {
                    $join->on('prod_procurement_document.document_ms_id', '=', 'prod_procurement_document_list_ms.procurement_document_list_id')
                        ->where('prod_procurement_document.procurement_id', $proc_id);
                })
                ->addSelect('prod_procurement_document.value')
                ->addSelect('prod_procurement_document.procurement_document_id as key')
                ->groupBy('prod_procurement_document.procurement_document_id');
        }

        $documents =
            $documents->orderBy('seq')
            ->groupByRaw('procurement_document_list_id')
            ->addSelect('prod_procurement_document_list_ms.*')
            ->get();

        $documents = $documents->map(function ($item) {
            if ($item['data_type'] == "document" && $item['value'] != null) {
                $item['value'] = url('') . Storage::url($item['value']);
            }
            return $item;
        });


        if ($process_id == null) {
            $documents = $documents->groupBy('process_id');
        }

        // var_dump($documents->toSql());



        return $documents;
    }

    public function getProcurementHistory(int $procurement_id): Collection | bool
    {
        // try {
        $row = DB::table('prod_procurement_history as ph')
            ->leftJoin('prod_procurement_process_ms as pp', 'ph.procurement_process_id', '=', 'pp.procurement_process_id')
            ->leftJoin('prod_users as du', 'du.id', '=', 'ph.user_id')
            ->selectRaw('du.name,pp.process_name,ph.status,ph.remark,ph."date",ph.procurement_process_id')
            ->where('ph.procurement_id',  '=', $procurement_id)
            ->get();

        if ($row->count() > 0) {
            return $row;
        } else {
            return false;
        }

        // } catch (Exception $e) {
        //     Log::build([
        //         'driver' => 'single',
        //         'path' => storage_path('logs/custom.log'),
        //     ])->info(json_encode($e->getMessage()));

        //     return false;
        // }
    }



    public function createApprovalList(int $procurement_id, int $division_id)
    {
        try {
            $approval_order = DB::table('prod_procurement_approval_order as pp')
                ->where('pp.division_id',  '=', $division_id)
                ->get();

            if ($approval_order->count() > 0) {
                $seq = 1;
                foreach ($approval_order as $order) {
                    DB::table('prod_procurement_approval_list')->insert(
                        [
                            "procurement_id"    => $procurement_id,
                            "position_id"       => $order->position_id,
                            "approval_status"   => $seq == 1 ? ApprovalStatus::NeedReview->value : null,
                            "sequences"         => $seq,
                            "last_updated"      => date('Y-m-d H:i:s')
                        ]
                    );
                    $seq++;
                }
            }

            return true;
        } catch (Exception $e) {
            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/custom.log'),
            ])->info(json_encode($e->getMessage()));

            return false;
        }
    }
}
