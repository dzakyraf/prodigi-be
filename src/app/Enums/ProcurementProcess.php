<?php

namespace App\Enums;

/**
 * Summary of ApprovalStatus
 */
enum ProcurementProcess: int
{

    case PERMOHONAN = 1;
    case DOKUMEN_RENDAN = 2;
    case DOKUMEN_LAKDAN = 3;
    case DOKUMEN_PENYELESAIAN = 4;
    case PEMBAYARAN = 5;
}
