<?php

namespace App\Services;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Jobs\SendNotifications;
use App\Models\Assets\AssetLog;
use App\Models\Assets\AssetsCatalog;
use App\Models\Finance\Budget\FmsBudget;
use App\Models\Finance\Invoice\FmsInvoice;
use App\Models\Finance\Invoice\FmsQuotation;
use App\Models\Assets\Settings\AssetCategory;
use App\Models\Procurement\Settings\Provider;
use App\Models\Inventory\Stock\InvStockItemCode;
use App\Models\Finance\Invoice\FmsInvoicePayment;
use App\Models\Finance\Settings\FmsFinancialYear;
use App\Models\Finance\Requests\FmsPaymentRequest;
use App\Models\Finance\Accounting\FmsLedgerAccount;
use App\Models\Finance\Transactions\FmsTransaction;
use App\Models\HumanResource\EmployeeData\Employee;
use App\Models\Procurement\Request\ProcurementRequest;
use App\Models\HumanResource\LeaveManagement\LeaveRequest;
use App\Models\Procurement\Settings\ProcurementSubcategory;
use App\Models\Grants\Project\ProjectEmployees\ProjectContract;
use App\Models\HumanResource\OfficialContract\OfficialContract;
use App\Models\HumanResource\EmployeeData\OfficialContract\ContractRenewalRequest;

class GeneratorService
{
    public static function password()
    {
        return Str::password(8);
    }

    public static function employeeNo()
    {
        $alphabets = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $emp_number = '';
        $randomAlphabetIndex = mt_rand(0, strlen($alphabets) - 1);
        $randomAlphabet = $alphabets[$randomAlphabetIndex];

        $latestEmpNo = Employee::select('employee_number')->orderBy('id', 'desc')->first();

        if ($latestEmpNo) {
            // $emp_number = 'BRC'.((int) filter_var($latestEmpNo->employee_number, FILTER_SANITIZE_NUMBER_INT) + 1).$randomAlphabet;
            $emp_number = 'BRC' . str_pad(((int) filter_var($latestEmpNo->employee_number, FILTER_SANITIZE_NUMBER_INT) + 1), 5, '0', STR_PAD_LEFT) . $randomAlphabet;
        } else {
            $emp_number = 'BRC00001' . $randomAlphabet;
        }

        return $emp_number;
    }

    public static function providerNo()
    {
        $provider_code = '';
        $yearStart = date('y');
        $latestProviderNo = Provider::select('provider_code')->orderBy('id', 'desc')->first();
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // All uppercase letters
        $randomAlphabet = $alphabet[rand(0, strlen($alphabet) - 1)];
        // $randomAlphabet = ucfirst(Str::random(1));

        if ($latestProviderNo) {
            $latestProviderNoSplit = explode('-', $latestProviderNo->provider_code);
            $providerYear = (int) filter_var($latestProviderNoSplit[0], FILTER_SANITIZE_NUMBER_INT);

            if ($providerYear == $yearStart) {
                $provider_code = $latestProviderNoSplit[0] . '-' . str_pad(((int) filter_var($latestProviderNoSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 3, '0', STR_PAD_LEFT) . $randomAlphabet;
            } else {
                $provider_code = $yearStart . 'PRV' . '-001' . $randomAlphabet;
            }
        } else {
            $provider_code = $yearStart . 'PRV' . '-001' . $randomAlphabet;
        }

        return $provider_code;
    }

    private static function incrementSubcategoryCode($code)
    {
        $parts = explode('/', $code);
        $lastPart = end($parts);
        $newLastPart = intval($lastPart) + 1;
        $newCode = implode('/', array_slice($parts, 0, -1)) . '/' . str_pad($newLastPart, 3, '0', STR_PAD_LEFT);
        return $newCode;
    }

    public static function procurementSubcategoryCode($category)
    {
        $categoryCode = '';

        switch ($category) {
            case 'Supplies':
                $categoryCode = 'Sup';
                break;
            case 'Services':
                $categoryCode = 'Svcs';
                break;
            case 'Works':
                $categoryCode = 'Works';
                break;
            case 'Consultancy':
                $categoryCode = 'Svcs';
                break;
            default:
                // Handle invalid category
                break;
        }

        // Retrieve the current category
        $latestSubcategory = ProcurementSubcategory::where('category', $category)->orderBy('id', 'desc')->first();
        if ($latestSubcategory) {
            return Self::incrementSubcategoryCode($latestSubcategory->code);
        } else {
            if ($category == 'Consultancy') {
                $categoryCount = ProcurementSubcategory::where('category', 'Services')->count() + 1;
                $categoryCode = $categoryCode . '/' . str_pad($categoryCount, 3, '0', STR_PAD_LEFT);

                $consultanceSubcategory = ProcurementSubcategory::create([
                    'category' => 'Services',
                    'code' => $categoryCode,
                    'name' => $category,
                ]);
                return $consultanceSubcategory->code . '/' . str_pad(1, 3, '0', STR_PAD_LEFT);

            } else {
                return $categoryCode . '/' . str_pad(1, 3, '0', STR_PAD_LEFT);
            }
        }
    }

    public static function budgetIdentifier()
    {
        $identifier = '';
        $yearStart = date('y');
        $characters = 'ABCDEFGHJKLMNOPQRSTUVWXYZ';
        $l = $characters[rand(0, strlen($characters) - 2)];
        $latestIdentifier = FmsBudget::select('code')->orderBy('id', 'desc')->first();

        if ($latestIdentifier) {
            $numberSplit = explode('-', $latestIdentifier->code);
            $numberYear = (int) filter_var($numberSplit[0], FILTER_SANITIZE_NUMBER_INT);

            if ($numberYear == $yearStart) {
                $identifier = $numberSplit[0] . '-' . str_pad(((int) filter_var($numberSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 4, '0', STR_PAD_LEFT) . $l;
            } else {
                $identifier = 'FMB' . $yearStart . '-0001' . $l;
            }
        } else {
            $identifier = 'FMB' . $yearStart . '-0001' . $l;
        }

        return $identifier;

    }

    public static function ledgerIdentifier()
    {
        $identifier = '';
        $yearStart = date('y');
        $characters = 'ABCDEFGHJKLMNOPQRSTUVWXYZ';
        $l = $characters[rand(0, strlen($characters) - 2)];
        $latestIdentifier = FmsLedgerAccount::select('account_number')->orderBy('id', 'desc')->first();

        if ($latestIdentifier) {
            $numberSplit = explode('-', $latestIdentifier->account_number);
            $numberYear = (int) filter_var($numberSplit[0], FILTER_SANITIZE_NUMBER_INT);

            if ($numberYear == $yearStart) {
                $identifier = $numberSplit[0] . '-' . str_pad(((int) filter_var($numberSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 4, '0', STR_PAD_LEFT) . $l;
            } else {
                $identifier = 'BRC-L' . $yearStart . '-0001' . $l;
            }
        } else {
            $identifier = 'BRC-L' . $yearStart . '-0001' . $l;
        }

        return $identifier;

    }

    public static function getInvNumber()
    {
        $identifier = '';
        $yearStart = date('y');
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $l = $characters[rand(0, strlen($characters) - 2)];
        $latestIdentifier = FmsInvoice::select('invoice_no')->orderBy('id', 'desc')->first();

        if ($latestIdentifier) {
            $numberSplit = explode('-', $latestIdentifier->invoice_no);
            $numberYear = (int) filter_var($numberSplit[0], FILTER_SANITIZE_NUMBER_INT);

            if ($numberYear == $yearStart) {
                $identifier = $numberSplit[0] . '-' . str_pad(((int) filter_var($numberSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 5, '0', STR_PAD_LEFT) . $l;
            } else {
                $identifier = 'INV' . $yearStart . '-00001' . $l;
            }
        } else {
            $identifier = 'INV' . $yearStart . '-00001' . $l;
        }

        return $identifier;
    }

    public static function getQuoteNumber()
    {
        $identifier = '';
        $yearStart = date('y');
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $l = $characters[rand(0, strlen($characters) - 2)];
        $latestIdentifier = FmsQuotation::select('quotation_no')->orderBy('id', 'desc')->first();

        if ($latestIdentifier) {
            $numberSplit = explode('-', $latestIdentifier->quotation_no);
            $numberYear = (int) filter_var($numberSplit[0], FILTER_SANITIZE_NUMBER_INT);

            if ($numberYear == $yearStart) {
                $identifier = $numberSplit[0] . '-' . str_pad(((int) filter_var($numberSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 5, '0', STR_PAD_LEFT) . $l;
            } else {
                $identifier = 'QTI' . $yearStart . '-00001' . $l;
            }
        } else {
            $identifier = 'QTI' . $yearStart . '-00001' . $l;
        }

        return $identifier;
    }

    public static function getRequestNumber()
    {
        $identifier = '';
        $yearStart = date('y');
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $l = $characters[rand(0, strlen($characters) - 2)];
        $latestIdentifier = FmsPaymentRequest::select('request_code')->orderBy('id', 'desc')->first();

        if ($latestIdentifier) {
            $numberSplit = explode('-', $latestIdentifier->request_code);
            $numberYear = (int) filter_var($numberSplit[0], FILTER_SANITIZE_NUMBER_INT);

            if ($numberYear == $yearStart) {
                $identifier = $numberSplit[0] . '-' . str_pad(((int) filter_var($numberSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 5, '0', STR_PAD_LEFT) . $l;
            } else {
                $identifier = 'BRCR' . $yearStart . '-00001' . $l;
            }
        } else {
            $identifier = 'BRCR' . $yearStart . '-00001' . $l;
        }

        return $identifier;
    }
    public static function getTrxNumber2()
    {
        $identifier = '';
        $yearStart = date('y');
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $l = $characters[rand(0, strlen($characters) - 2)];
        $latestIdentifier = FmsTransaction::select('trx_no')->orderBy('id', 'desc')->first();

        if ($latestIdentifier) {
            $numberSplit = explode('-', $latestIdentifier->trx_no);
            $numberYear = (int) filter_var($numberSplit[0], FILTER_SANITIZE_NUMBER_INT);

            if ($numberYear == $yearStart) {
                $identifier = $numberSplit[0] . '-' . str_pad(((int) filter_var($numberSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 6, '0', STR_PAD_LEFT) . $l;
            } else {
                $identifier = 'TRX' . $yearStart . '-000001' . $l;
            }
        } else {
            $identifier = 'TRX' . $yearStart . '-000001' . $l;
        }

        return $identifier;
    }
    public static function getTrxNumber()
    {
        $yearStart = date('Ym'); // YYYYMM format
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $maxAttempts = 10;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $randomChar = $characters[random_int(0, strlen($characters) - 1)];
            $latestIdentifier = FmsTransaction::select('trx_no')->orderBy('id', 'desc')->first();

            if ($latestIdentifier) {
                $numberSplit = substr($latestIdentifier->trx_no, 0, 13); // TRXYYYYMM-0001X
                $numberYear = substr($numberSplit, 3, 6); // YYYYMM

                if ($numberYear === $yearStart) {
                    $sequenceNumber = (int) substr($latestIdentifier->trx_no, 10, 4); // Extract sequence number
                    $newNumber = str_pad($sequenceNumber + 1, 4, '0', STR_PAD_LEFT);
                    $identifier = 'TRX' . $yearStart . '-' . $newNumber . $randomChar;
                } else {
                    $identifier = 'TRX' . $yearStart . '-001' . $randomChar;
                }
            } else {
                $identifier = 'TRX' . $yearStart . '-001' . $randomChar;
            }

            // Check if the identifier is unique
            if (!FmsTransaction::where('trx_no', $identifier)->exists()) {
                return $identifier;
            }
        }

        throw new Exception("Unable to generate a unique transaction number after $maxAttempts attempts.");
    }

    public static function getReceiptNumber()
    {
        $identifier = '';
        $yearStart = date('y');
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $l = $characters[rand(0, strlen($characters) - 2)];
        $latestIdentifier = FmsInvoicePayment::select('receipt_no')->orderBy('id', 'desc')->first();

        if ($latestIdentifier) {
            $numberSplit = explode('-', $latestIdentifier->receipt_no);
            $numberYear = (int) filter_var($numberSplit[0], FILTER_SANITIZE_NUMBER_INT);

            if ($numberYear == $yearStart) {
                $identifier = $numberSplit[0] . '-' . str_pad(((int) filter_var($numberSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 5, '0', STR_PAD_LEFT) . $l;
            } else {
                $identifier = 'RCT' . $yearStart . '-00001' . $l;
            }
        } else {
            $identifier = 'RCT' . $yearStart . '-00001' . $l;
        }

        return $identifier;
    }

    public static function getNumber($length)
    {
        $characters = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return str_shuffle($randomString);
        return $randomString;
    }

    //Generate a request code
    public static function requestCode()
    {
        $yearMonth = date('ym');
        $characters = 'ABCDEFGHJKLMNOPQRSTUVWXYZ123456789';
        $l = $characters[rand(2, strlen($characters) - 4)];
        $randomGeneratedNumber = intval('0' . mt_rand(1, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9));

        return 'MERP-RQ/' . $yearMonth . '-' . $randomGeneratedNumber . '-' . $l;
    }

    // public static function procurementRequestRef($category)
    // {
    //     $categoryCode = '';

    //     switch ($category) {
    //         case 'Supplies':
    //             $categoryCode = 'SUP';
    //             break;
    //         case 'Services':
    //             $categoryCode = 'SVCS';
    //             break;
    //         case 'Works':
    //             $categoryCode = 'WKS';
    //             break;
    //         case 'Consultancy':
    //             $categoryCode = 'CONS';
    //             break;
    //         default:
    //             // Handle invalid category
    //             break;
    //     }

    //     $requestRef = '';
    //     $yearStart = date('y');
    //     $latestRef = ProcurementRequest::where('procurement_sector',$category)->select('reference_no')->orderBy('id', 'desc')->first();
    //     $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    //     $randomAlphabet = $characters[rand(0, strlen($characters) - 1)];
    //     // $randomAlphabet = ucfirst(Str::random(1));

    //     if ($latestRef) {
    //         $latestRefSplit = explode('-', $latestRef->reference_no);
    //         $refYear = (int) filter_var($latestRefSplit[0], FILTER_SANITIZE_NUMBER_INT);

    //         if ($refYear == $yearStart) {
    //             $requestRef = $latestRefSplit[0].'-'.str_pad(((int) filter_var($latestRefSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 5, '0', STR_PAD_LEFT).$randomAlphabet;
    //         } else {
    //             $requestRef = $yearStart.$categoryCode.'-00001'.$randomAlphabet;
    //         }
    //     } else {
    //         $requestRef = $yearStart.$categoryCode.'-00001'.$randomAlphabet;
    //     }

    //     return $requestRef;
    // }

    public static function procurementRequestRef($category, $financialYearId, $processing = false)
    {
        $sequenceNumber = '';

        $categoryCode = '';

        switch ($category) {
            case 'Supplies':
                $categoryCode = 'SUP';
                break;
            case 'Services':
                $categoryCode = 'SVCS';
                break;
            case 'Works':
                $categoryCode = 'WKS';
                break;
            case 'Consultancy':
                $categoryCode = 'CONS';
                break;
            default:
                // Handle invalid category
                break;
        }

        $financialYear = FmsFinancialYear::where('id', $financialYearId)->first();
        $financialYear = explode('_', $financialYear->name)[1];

        $latestCategoryRequest = ProcurementRequest::where(['procurement_sector' => $category, 'financial_year_id' => $financialYearId])->whereNotNull('sequence_number')->orderBy('sequence_number', 'desc')->first();

        // if ($latestCategoryRequest) {
        //     $sequenceNumber = str_pad(((int) filter_var($latestCategoryRequest->sequence_number, FILTER_SANITIZE_NUMBER_INT) + 1), 5, '0', STR_PAD_LEFT);
        // } else {
        //     //check if the request has reached procurement officer so that the final sequence number can be generated
        //     if ($processing) {
        //         $sequenceNumber = '00001';
        //     } else {
        //         $sequenceNumber = rand(10000, 20000);
        //     }

        // }

        if ($processing) {
            if ($latestCategoryRequest) {
                $sequenceNumber = str_pad(((int) filter_var($latestCategoryRequest->sequence_number, FILTER_SANITIZE_NUMBER_INT) + 1), 5, '0', STR_PAD_LEFT);
            } else {
                $sequenceNumber = '00001';
            }

        } else {
            $sequenceNumber = rand(10000, 20000);
        }

        $procurementRef = 'MAKBRC' . '/' . $categoryCode . '/' . $financialYear . '/' . $sequenceNumber;

        return ['sequenceNumber' => $sequenceNumber, 'procurementRef' => $procurementRef];
    }

    // public static function procurementRequestRef($category)
    // {
    //     $categoryCode = '';

    //     switch ($category) {
    //         case 'Supplies':
    //             $categoryCode = 'SUP';
    //             break;
    //         case 'Services':
    //             $categoryCode = 'SVCS';
    //             break;
    //         case 'Works':
    //             $categoryCode = 'WKS';
    //             break;
    //         case 'Consultancy':
    //             $categoryCode = 'CONS';
    //             break;
    //         default:
    //             // Handle invalid category
    //             break;
    //     }

    //     $requestRef = '';
    //     $yearStart = date('y');
    //     $latestRef = ProcurementRequest::where('procurement_sector',$category)->select('reference_no')->orderBy('id', 'desc')->first();
    //     $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    //     $randomAlphabet = $characters[rand(0, strlen($characters) - 1)];
    //     // $randomAlphabet = ucfirst(Str::random(1));

    //     if ($latestRef) {
    //         $latestRefSplit = explode('-', $latestRef->reference_no);
    //         $refYear = (int) filter_var($latestRefSplit[0], FILTER_SANITIZE_NUMBER_INT);

    //         if ($refYear == $yearStart) {
    //             $requestRef = $latestRefSplit[0].'-'.str_pad(((int) filter_var($latestRefSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 5, '0', STR_PAD_LEFT).$randomAlphabet;
    //         } else {
    //             $requestRef = $yearStart.$categoryCode.'-00001'.$randomAlphabet;
    //         }
    //     } else {
    //         $requestRef = $yearStart.$categoryCode.'-00001'.$randomAlphabet;
    //     }

    //     return $requestRef;
    // }

    public static function localPurchaseOrderNo()
    {
        $lpoNo = null;
        $yearStart = date('y');

        $latestLpoNo = ProcurementRequest::whereNotNull('lpo_no')->orderBy('lpo_no', 'desc')->first();

        if ($latestLpoNo) {
            // $latestLpoNoSplit = explode('-', $latestLpoNo->lpo_no);
            $year = substr($latestLpoNo->lpo_no, 0, 2);
            $number = substr($latestLpoNo->lpo_no, 2);
            $lpoYear = (int) filter_var($year, FILTER_SANITIZE_NUMBER_INT);

            if (intVal($lpoYear) == $yearStart) {
                $lpoNo = $yearStart . '' . str_pad(((int) filter_var($number, FILTER_SANITIZE_NUMBER_INT) + 1), 4, '0', STR_PAD_LEFT);
            } else {
                $lpoNo = $yearStart . '0001';
            }
        } else {
            $lpoNo = $yearStart . '0001';
        }

        return $lpoNo;
    }

    public static function goodsReceivedNoteNo()
    {
        $grnNo = null;
        $yearStart = date('y');

        $latestGrnNo = ProcurementRequest::whereNotNull('grn_no')->orderBy('grn_no', 'desc')->first();

        if ($latestGrnNo) {
            $year = substr($latestGrnNo->grn_no, 0, 2);
            $number = substr($latestGrnNo->grn_no, 2);
            $grnYear = (int) filter_var($year, FILTER_SANITIZE_NUMBER_INT);

            if (intVal($grnYear) == $yearStart) {
                $grnNo = $yearStart . '' . str_pad(((int) filter_var($number, FILTER_SANITIZE_NUMBER_INT) + 1), 4, '0', STR_PAD_LEFT);
            } else {
                $grnNo = $yearStart . '0001';
            }
        } else {
            $grnNo = $yearStart . '0001';
        }

        return $grnNo;
    }

    public static function assetLabel($departmentCode, $assetCategoryId)
    {
        $labelName = null;
        $assetCategory = AssetCategory::findOrFail($assetCategoryId);

        $latestCategoryAsset = AssetsCatalog::where(['asset_category_id' => $assetCategory->id])->where('asset_name', '!=', '1')->orderBy('id', 'desc')->first();

        if ($latestCategoryAsset) {
            $latestCategoryAssetNameSplit = explode('-', $latestCategoryAsset->asset_name);

            $labelName = $departmentCode . '-' . $assetCategory->short_code . '-' . str_pad(((int) filter_var(end($latestCategoryAssetNameSplit), FILTER_SANITIZE_NUMBER_INT) + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $labelName = $departmentCode . '-' . $assetCategory->short_code . '-0001';
        }

        return $labelName;
    }

    public static function assetBreakdownNumber($assetsCatalogId)
    {
        $breakdownNumber = null;
        $assetsCatalog = AssetsCatalog::findOrFail($assetsCatalogId);
        $latestBreakdown = AssetLog::where(['asset_catalog_id' => $assetsCatalog->id, 'log_type' => 'Breakdown'])->orderBy('id', 'desc')->first();

        if ($latestBreakdown) {
            $latestBreakdownNumberSplit = explode('-', $latestBreakdown->breakdown_number);

            $breakdownNumber = $assetsCatalog->asset_name . '-BKD' . str_pad(((int) filter_var(end($latestBreakdownNumberSplit), FILTER_SANITIZE_NUMBER_INT) + 1), 3, '0', STR_PAD_LEFT);
        } else {
            $breakdownNumber = $assetsCatalog->asset_name . '-BKD001';
        }

        return $breakdownNumber;
    }

    public static function StockBarcodeod()
    {
        $lastItemId = InvStockItemCode::count();
        $barcode = 'BRC-ISQ-0' . ($lastItemId + 1);
        return $barcode;
    }

    public static function StockBarcode()
    {
        // Generate a random 2-letter string (assuming ISQ is 2 letters)
        $randomISQ = Str::upper(Str::random(2));

        // Generate a unique identifier for today (e.g., using an incrementing value or random number)
        $lastNumber = InvStockItemCode::count();
        $uniqueDayPart = date('d') . '-' . ($lastNumber + 1);

        // Generate the new barcode
        $barcode = 'BRC-' . $randomISQ . '-' . $uniqueDayPart;

        // Check if the barcode already exists
        while (InvStockItemCode::where('code', $barcode)->exists()) {
            // If it exists, regenerate the random part and try again
            $randomISQ = Str::upper(Str::random(2));
            $uniqueDayPart = date('d') . '-' . ($lastNumber + 1);
            $barcode = 'BRC-' . $randomISQ . '-' . $uniqueDayPart;
        }

        return $barcode;
    }

    public static function generateInitials(string $name)
    {
        $n = Str::of($name)->wordCount();
        $words = explode(' ', $name);

        if (count($words) <= 2) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8') .
                mb_substr(end($words), 0, 1, 'UTF-8'),
                'UTF-8');
        } elseif (count($words) == 3) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8') .
                mb_substr($words[1], 0, 1, 'UTF-8') .
                mb_substr(end($words), 0, 1, 'UTF-8'),
                'UTF-8');
        } elseif (count($words) == 4) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8') .
                mb_substr($words[1], 0, 1, 'UTF-8') .
                mb_substr($words[2], 0, 1, 'UTF-8') .
                mb_substr(end($words), 0, 1, 'UTF-8'),
                'UTF-8');
        } elseif (count($words) == 5) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8') .
                mb_substr($words[1], 0, 1, 'UTF-8') .
                mb_substr($words[2], 0, 1, 'UTF-8') .
                mb_substr($words[3], 0, 1, 'UTF-8') .
                mb_substr(end($words), 0, 1, 'UTF-8'),
                'UTF-8');
        } elseif (count($words) == 6) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8') .
                mb_substr($words[1], 0, 1, 'UTF-8') .
                mb_substr($words[2], 0, 1, 'UTF-8') .
                mb_substr($words[3], 0, 1, 'UTF-8') .
                mb_substr($words[4], 0, 1, 'UTF-8') .
                mb_substr(end($words), 0, 1, 'UTF-8'),
                'UTF-8');
        } elseif (count($words) == 7) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8') .
                mb_substr($words[1], 0, 1, 'UTF-8') .
                mb_substr($words[2], 0, 1, 'UTF-8') .
                mb_substr($words[3], 0, 1, 'UTF-8') .
                mb_substr($words[4], 0, 1, 'UTF-8') .
                mb_substr($words[5], 0, 1, 'UTF-8') .
                mb_substr(end($words), 0, 1, 'UTF-8'),
                'UTF-8');
        } elseif (count($words) == 8) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8') .
                mb_substr($words[1], 0, 1, 'UTF-8') .
                mb_substr($words[2], 0, 1, 'UTF-8') .
                mb_substr($words[3], 0, 1, 'UTF-8') .
                mb_substr($words[4], 0, 1, 'UTF-8') .
                mb_substr($words[5], 0, 1, 'UTF-8') .
                mb_substr($words[6], 0, 1, 'UTF-8') .
                mb_substr(end($words), 0, 1, 'UTF-8'),
                'UTF-8');
        } elseif (count($words) >= 9) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8') .
                mb_substr($words[1], 0, 1, 'UTF-8') .
                mb_substr($words[2], 0, 1, 'UTF-8') .
                mb_substr($words[3], 0, 1, 'UTF-8') .
                mb_substr($words[4], 0, 1, 'UTF-8') .
                mb_substr($words[5], 0, 1, 'UTF-8') .
                mb_substr($words[6], 0, 1, 'UTF-8') .
                mb_substr($words[7], 0, 1, 'UTF-8') .
                mb_substr(end($words), 0, 1, 'UTF-8'),
                'UTF-8');
        }

        return self::makeInitialsFromSingleWord($name);
    }

    protected static function makeInitialsFromSingleWord(string $name)
    {
        $n = Str::of($name)->wordCount();
        preg_match_all('#([A-Z]+)#', $name, $capitals);
        if (count($capitals[1]) >= $n) {
            return mb_substr(implode('', $capitals[1]), 0, $n, 'UTF-8');
        }

        return mb_strtoupper(mb_substr($name, 0, $n, 'UTF-8'), 'UTF-8');
    }

    public static function convertToWords($amount, $currency = 'USD')
    {
        $currencies = [
            'USD' => ['dollar', 'cent'],
            'EURO' => ['euro', 'cent'],
            'GBP' => ['pound', 'pence'],
            'UGX' => ['shilling', 'cent'],
        ];

        list($majorName, $minorName) = $currencies[strtoupper($currency)] ?? ['dollar', 'cent'];

        $majorAmount = floor($amount);
        $minorAmount = round(($amount - $majorAmount) * 100);

        $majorWords = self::numberToWords($majorAmount) . ' ' . ($majorAmount == 1 ? $majorName : $majorName . 's');
        $minorWords = $minorAmount > 0 ? ' and ' . self::numberToWords($minorAmount) . ' ' . ($minorAmount == 1 ? $minorName : $minorName . 's') : '';

        return ucfirst($majorWords . $minorWords);
    }

    public static function numberToWords($number)
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion',
        ];

        if (!is_numeric($number)) {
            return false;
        }

        if ($number < 0) {
            return $negative . self::numberToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::numberToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::numberToWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    public static function leaveRequestRef()
    {
        $requestRef = '';
        $yearStart = date('y');
        $latestRef = LeaveRequest::select('leave_ref')->orderBy('id', 'desc')->first();
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $randomAlphabet = $characters[rand(0, strlen($characters) - 1)];

        if ($latestRef) {
            $latestRefSplit = explode('-', $latestRef->leave_ref);
            $refYear = (int) filter_var($latestRefSplit[0], FILTER_SANITIZE_NUMBER_INT);

            if ($refYear == $yearStart) {
                $requestRef = $latestRefSplit[0] . '-' . str_pad(((int) filter_var($latestRefSplit[1], FILTER_SANITIZE_NUMBER_INT) + 1), 4, '0', STR_PAD_LEFT) . $randomAlphabet;
            } else {
                $requestRef = $yearStart . 'LV' . '-0001' . $randomAlphabet;
            }
        } else {
            $requestRef = $yearStart . 'LV' . '-0001' . $randomAlphabet;
        }

        return $requestRef;
    }

    public static function contractRenewalRequestRef(OfficialContract $officialContract)
    {
        return $officialContract->contract_no.'/RQ';
    }

    public static function contractNo($year, $contractNumber,$contractType,$workType)
    {
        $contractNo = '';
        $number = str_pad($contractNumber, 4, '0', STR_PAD_LEFT);
        $contractNo = 'MAKBRC/HR/' . $year . "/CONT/" .$contractType. $number . $workType;
        return  $contractNo ;
    }

    public static function contractNumber($contractType,$workType,$contractable=null)
    {
        $contractNo = '';
        $year = date('y');
         $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $randomAlphabet = $characters[rand(0, strlen($characters) - 1)];
        if ($contractable=='Project') {
            $existingCount = ProjectContract::whereYear('created_at', date('Y'))
        ->count();
        }else{
            $existingCount = OfficialContract::whereYear('created_at', date('Y'))
            ->count();
        }
      
        // Increment the count and pad with zeros
        $number = str_pad($existingCount + 1, 3, '0', STR_PAD_LEFT);
        $contractNo = 'MAKBRC/HR/' . $year . "/CONT/" .$contractType. $number . $workType.$randomAlphabet;

        return  $contractNo ;
    }

    public static function SendMail($id, $body, $subject, $link)
    {
        try {
            $user = User::where('id', $id)->first();
            $link = $link;
            $notification = [
                'to' => $user->email,
                'phone' => $user->contact,
                'subject' => $subject ?? 'MERP',
                'greeting' => 'Dear ' . $user->title . ' ' . $user->name,
                'body' => $body,
                'thanks' => 'Thank you, incase of any question, please reply to support@makbrc.org',
                'actionText' => 'View Details',
                'actionURL' => $link,
                'department_id' => auth()->user()->id ?? null,
                'user_id' => auth()->user()->id ?? null,
            ];
            // WhatAppMessageService::sendReferralMessage($referral_request);
            $mm = SendNotifications::dispatch($notification)->delay(Carbon::now()->addSeconds(20));
            //   dd($mms);
        } catch (Throwable $error) {
            // $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Referral Request '.$error.'!']);
        }
        // $this->dispatchBrowserEvent('alert', ['type' => 'Success', 'message' => 'Document has been successfully marked complete! ']);
    }
}
