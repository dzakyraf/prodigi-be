<?php

namespace App\Enums;

/**
 * Summary of ApprovalStatus
 */
enum ApprovalStatus: string
{
    case NeedReview = 'need-review';
    case Rejected = 'rejected';
    case NeedRevision = 'need-revise';
    case Approved = 'approved';
    case OnDraft = 'on-draft';
    case DraftComplete = 'document-complete';
    case ProcurementComplete = 'procurement-complete';

    public function name(): string
    {
        return match ($this) {
            ApprovalStatus::OnDraft => 'Dalam Proses',
            ApprovalStatus::DraftComplete => 'Dokumen Lengkap',
            ApprovalStatus::NeedReview => 'Butuh Persetujuan',
            ApprovalStatus::Rejected => 'Dibatalkan',
            ApprovalStatus::NeedRevision => 'Butuh Perbaikan',
            ApprovalStatus::Approved => 'Disetujui',
            ApprovalStatus::ProcurementComplete => 'Selesai Diproeses',
        };
    }
}
