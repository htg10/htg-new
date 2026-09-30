<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\SMSController;
use App\Mail\ConfirmMail;
use App\Models\Bank;
use App\Models\Telecaller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Entry;
use App\Models\Products;
use App\Models\EntryHistory;
use App\Mail\SendMail;
use App\Mail\RenewalEmail;
use Carbon\Carbon;
use DB;
use ZipArchive;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdminExport;
use App\Models\User;
use App\Models\PaymentHistory;

class DashboardController extends Controller
{
    
    public function dashboard()
    {
        $entry = Entry::where('date', '>=', '2025-09-01')->get();
        $services = Products::whereHas('entry', fn($q) => $q->where('date', '>=', '2025-09-01'))->get();

        $entries = Entry::with('product')->where('date', '>=', '2025-09-01')->get();

        $totalAmount = $entries->reduce(function ($carry, $entry) {
            return $carry + ($entry->product ? $entry->product->sum('total_amount') : 0);
        }, 0);

        $totalBalance = $entries->reduce(function ($carry, $entry) {
            return $carry + ($entry->product ? $entry->product->sum('total_amount') - $entry->product->sum('paid_amount') : 0);
        }, 0);

        return view('admin.dashboard', compact('entry', 'services', 'totalAmount', 'totalBalance'));
    }

    
    public function index(Request $request)
    {
        $query = Entry::with('product', 'user')
            ->where('date', '>=', '2025-09-01');

        // Apply filters if any
        if ($request->has('company')) {
            $query->where('company', 'like', '%' . $request->company . '%');
        }

        if ($request->has('year') && $request->year) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->has('month') && $request->month) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->has('inputState') && !empty($request->inputState)) {
            if ($request->inputState === 'Expired') {
                // $query->whereHas('product', function ($query) {
                //     $query->where('expiry_date', '<', now());
                // });

                if ($request->has('expiry_month') && $request->has('expiry_year')) {
                    $expiryMonth = $request->expiry_month;
                    $expiryYear = $request->expiry_year;

                    $query->whereHas('product', function ($query) use ($expiryMonth, $expiryYear) {
                        $query->whereMonth('expiry_date', $expiryMonth)
                            ->whereYear('expiry_date', $expiryYear);
                    });
                } else {
                    // Default expired (already expired)
                    $query->whereHas('product', function ($query) {
                        $query->where('expiry_date', '<', now());
                    });
                }
            } elseif ($request->inputState === 'Pending') {
                $query->whereHas('product', function ($query) {
                    $query->whereRaw('total_amount - paid_amount > 0');
                });
            }
        }

        $query->orderBy('created_at', 'desc');

        // Paginate entries
        $entries = $query->get();

        $entries = $entries->map(function ($entry) {
            if (is_null($entry->user)) {
                $entry->user = User::where('name', 'Atul')->first();
            }
            return $entry;
        });


        // Calculate totals for entries
        foreach ($entries as $entry) {
            $entry->totalAmount = 0;
            $entry->totalPaid = 0;

            if ($entry->product && $entry->product->isNotEmpty()) {
                $entry->totalAmount = $entry->product->sum('total_amount');
                $entry->totalPaid = $entry->product->sum('paid_amount');
            }
            $entry->balancePayment = $entry->totalAmount - $entry->totalPaid;
        }

        $totalAmount = $entries->sum('totalAmount');
        $totalBalance = $entries->sum('balancePayment');

        // For Type (New/Renew)
        $result = $query->selectRaw('type, count(*) as total_entry')
            ->groupBy('type')
            ->get();

        // For BDM Name (Filtered by the current page entries)
        $bdmData = $entries->groupBy('user.name')->map(function ($group) {
            return $group->count();
        });

        $productData = $entries->flatMap(function ($entry) {
            return $entry->product->map(function ($product) {
                return $product->product_name;
            });
        })->countBy();

        // dd($productData);
        // Prepare chart data for "Type"
        $chartData = "";
        foreach ($result as $list) {
            $chartData .= "['" . $list->type . "', " . $list->total_entry . "],";
        }

        // Prepare chart data for "BDM Name"
        $bdmDataString = "";
        foreach ($bdmData as $bdmName => $count) {
            $bdmDataString .= "['" . $bdmName . "', " . $count . "],";
        }

        // Prepare chart data for "Product Name"
        $productDataString = "";
        foreach ($productData as $productName => $count) {
            $productDataString .= "['" . $productName . " [" . $count . "]', " . $count . "],";
        }


        // Remove the trailing comma
        $chartData = rtrim($chartData, ',');
        $bdmDataString = rtrim($bdmDataString, ',');
        $productDataString = rtrim($productDataString, ',');

        return view('admin.index', compact('entries', 'totalAmount', 'totalBalance', 'chartData', 'bdmDataString', 'productDataString'));
    }


    public function addNew()
    {
        $users = User::where('role_id', 2)->get();
        $banks = Bank::all();
        return view('admin.addnew', compact('users', 'banks'));
    }

    public function renew()
    {
        $users = User::where('role_id', 2)->get();
        $banks = Bank::all();
        return view('admin.renew', compact('users', 'banks'));
    }

    public function getCompanySuggestions(Request $request)
    {
        $query = $request->get('query');

        if (!$query) {
            return response()->json(['companies' => []]);
        }

        // Get companies that match the query
        $companies = Entry::where('company', 'LIKE', "%$query%")
            ->limit(10)
            ->get(['id', 'company']);

        return response()->json(['companies' => $companies]);
    }

    public function getCompanyData($name)
    {
        $company = Entry::where('company', 'like', '%' . $name . '%')->first();

        if ($company) {
            return response()->json($company);
        }

        return response()->json(null);
        // return response()->json($company);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'user_id' => 'required|exists:users,id',
        ]);

        $products = $request->input('products', []);

        $data = $request->except('products');

        if ($request->hasFile('image')) {
            $fileImages = $request->file('image');
            $imagePaths = [];

            foreach ($fileImages as $fileImage) {
                $fileImageName = rand() . '.' . $fileImage->getClientOriginalName();
                $fileImage->storeAs('images/', $fileImageName, );
                $imagePaths[] = 'storage/images/' . $fileImageName;
            }
            $data['image'] = json_encode($imagePaths);
        }

        $inputDate = $request->input('date') ?? now()->toDateString();
        $data['date'] = $inputDate;
        $entry = Entry::create($data);
        $startDate = Carbon::parse($inputDate);

        foreach ($products as $product) {
            if (isset($product['name'])) {

                $validity = $product['validity'];
                $expiryDate = null;

                $currentStartDate = $startDate->copy();

                switch ($validity) {
                    case '1 Month':
                        $expiryDate = $currentStartDate->addMonth()->format('Y-m-d');
                        break;
                    case '3 Months':
                        $expiryDate = $currentStartDate->addMonths(3)->format('Y-m-d');
                        break;
                    case '4 Months':
                        $expiryDate = $currentStartDate->addMonths(4)->format('Y-m-d');
                        break;
                    case '6 Months':
                        $expiryDate = $currentStartDate->addMonths(6)->format('Y-m-d');
                        break;
                    case '12 Months':
                        $expiryDate = $currentStartDate->addYear()->format('Y-m-d');
                        break;
                    case '24 Months':
                        $expiryDate = $currentStartDate->addMonths(24)->format('Y-m-d');
                        break;
                    case '36 Months':
                        $expiryDate = $currentStartDate->addMonths(36)->format('Y-m-d');
                        break;
                    case 'Lifetime':
                        $expiryDate = $currentStartDate->addYears(100)->format('Y-m-d');
                        break;
                    default:
                        break;
                }

                // dd($expiryDate);
                $newProduct = Products::create([
                    'product_name' => $product['name'],
                    'total_amount' => $product['total_amount'],
                    'paid_amount' => $product['paid_amount'],
                    'balance_amount' => $product['total_amount'] - $product['paid_amount'],
                    'validity' => $validity,
                    'expiry_date' => $expiryDate,
                    'payment_date' => $inputDate,
                    'entry_id' => $entry->id,
                ]);

                if ($product['paid_amount'] > 0) {
                    PaymentHistory::create([
                        'entry_id' => $entry->id,
                        'product_id' => $newProduct->id,
                        'product_name' => $product['name'],
                        'amount' => $product['paid_amount'],
                        'payment_bank' => $request->input('payment'),
                        'payment_date' => $inputDate,
                    ]);
                }

                // for mail
                $productDetails[] = [
                    'product_name' => $product['name'] ?? 'null',
                    'total_amount' => $product['total_amount'] ?? 0,
                    'paid_amount' => $product['paid_amount'] ?? 0,
                    'balance_amount' => $product['total_amount'] - $product['paid_amount'],
                    'validity' => $validity,
                    'expiry_date' => $expiryDate
                ];
            }
        }

        if ($data['type'] === 'new') {
            $smsTemplateId = env('NEW_SERVICE_TEMPLATE_ID');
            $message = "Welcome to Help Together Group, " . $entry->contact . "! Thank you for choosing Services.";
            SMSController::sendSms($smsTemplateId, $message, $entry->contactno);
            // dd($smsTemplateId, $message, $entry->contactno);
            Mail::to($data['email'])->send(new SendMail($data, $productDetails));

            // Send email to the admin
            $adminEmail = env('ADMIN_EMAIL');
            Mail::to($adminEmail)->send(new SendMail($data, $productDetails));
        } else {
            $smsTemplateId = env('RENEWAL_TEMPLATE_ID');
            $message = "Thank you for renewing your Services with Help Together Group! We're glad to have you continue with us. For any support, reach out anytime: +91 96346 44622";
            SMSController::sendSms($smsTemplateId, $message, $entry->contactno);
            Mail::to($data['email'])->send(new ConfirmMail($data, $productDetails));

            // Send email to the admin
            $adminEmail = env('ADMIN_EMAIL');
            Mail::to($adminEmail)->send(new ConfirmMail($data, $productDetails));
        }

        if ($request->filled('telecaller_id')) {
            Telecaller::where('id', $request->telecaller_id)
                ->update(['status' => 'CONVERTED']);
        }

        return redirect('/index')->with('success', 'Add successfully.');
    }

    public function downloadImages($id)
    {
        $entry = DB::table('entries')->where('id', $id)->first();

        if (!$entry || empty($entry->image)) {
            return response()->json(['error' => 'No images found'], 404);
        }

        $images = json_decode($entry->image);
        $files = [];

        foreach ($images as $image) {
            $filePath = public_path($image);
            if (file_exists($filePath)) {
                $files[] = $filePath;
            }
        }

        if (empty($files)) {
            return response()->json(['error' => 'No valid images found'], 404);
        }

        $zipFileName = "images_{$id}.zip";
        $zipPath = public_path("storage/images/{$zipFileName}");
        $zip = new ZipArchive;
        // dd($zipPath);

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        } else {
            return response()->json(['error' => 'Failed to create ZIP file'], 500);
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function edit($id)
    {

        $totalAmount = Products::where('entry_id', $id)->sum('total_amount');
        $totalBalance = Products::where('entry_id', $id)->sum('paid_amount');
        $totalPayment = $totalAmount - $totalBalance;

        // dd($totalAmount, $totalBalance, $totalPayment);
        $entries = Entry::with('product')->find($id);
        $products = Products::where('entry_id', $id)->get();
        $users = User::where('role_id', 2)->get();
        $banks = Bank::all();
        //dd($products);
        return view('admin.edit', compact('entries', 'products', 'totalAmount', 'totalBalance', 'totalPayment', 'users', 'banks'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // validation rules
        ]);

        $entry = Entry::findOrFail($id);
        $data = $request->all();

        $products = $request->input('products', []);
        $data = $request->except('products');

        if ($request->hasFile('image')) {
            $oldImages = json_decode($entry->image, true);

            if (!empty($oldImages)) {
                $imagePaths = $oldImages;
            } else {
                $imagePaths = [];
            }

            // Handle new images upload
            $fileImages = $request->file('image');
            foreach ($fileImages as $fileImage) {
                $fileImageName = rand() . '.' . $fileImage->getClientOriginalName();
                $fileImage->storeAs('images/', $fileImageName);
                $imagePaths[] = 'storage/images/' . $fileImageName;
            }


            $data['image'] = json_encode($imagePaths);
        }

        $entry->update($data);

        $date = $data['date'];
        $startDate = Carbon::parse($date);
        // $startDate = Carbon::now();

        foreach ($products as $product) {
            if (isset($product['name'])) {

                $validity = $product['validity'];
                $expiryDate = null;

                $currentStartDate = $startDate->copy();

                switch ($validity) {
                    case '1 Month':
                        $expiryDate = $currentStartDate->addMonth()->format('Y-m-d');
                        break;
                    case '3 Months':
                        $expiryDate = $currentStartDate->addMonths(3)->format('Y-m-d');
                        break;
                    case '4 Months':
                        $expiryDate = $currentStartDate->addMonths(4)->format('Y-m-d');
                        break;
                    case '6 Months':
                        $expiryDate = $currentStartDate->addMonths(6)->format('Y-m-d');
                        break;
                    case '12 Months':
                        $expiryDate = $currentStartDate->addYear()->format('Y-m-d');
                        break;
                    case '24 Months':
                        $expiryDate = $currentStartDate->addMonths(24)->format('Y-m-d');
                        break;
                    case '36 Months':
                        $expiryDate = $currentStartDate->addMonths(36)->format('Y-m-d');
                        break;
                    case 'Lifetime':
                        $expiryDate = $currentStartDate->addYears(100)->format('Y-m-d');
                        break;
                    default:
                        break;
                }

                // dd($expiryDate);
                $product_old = Products::where('entry_id', $entry->id)->where('product_name', $product['name'])->first();

                $updatedProduct = Products::updateOrCreate(
                    ['entry_id' => $entry->id, 'product_name' => $product['name']],
                    [
                        'total_amount' => $product['total_amount'],
                        'paid_amount' => ($product_old ? $product_old->paid_amount : 0) + $product['paid_amount'],
                        'balance_amount' => ($product_old ? $product_old->balance_amount : 0) - $product['paid_amount'],
                        'validity' => $validity,
                        'expiry_date' => $expiryDate,
                        'payment_date' => $product['paid_amount'] > 0 ? Carbon::now()->format('Y-m-d') : ($product_old ? $product_old->payment_date : null),
                    ]
                );

                if ($product['paid_amount'] > 0) {
                    PaymentHistory::create([
                        'entry_id' => $entry->id,
                        'product_id' => $updatedProduct->id,
                        'product_name' => $product['name'],
                        'amount' => $product['paid_amount'],
                        'payment_bank' => $product['payment_bank'] ?? null,
                        'payment_date' => Carbon::now()->format('Y-m-d'),
                    ]);
                }
            }
        }

        return redirect('/index')->with('success', 'Update successfully.');
    }

    function delete($id)
    {
        $entry = Entry::find($id);
        $entry->delete();
        return redirect('/index')->with('success', 'Delete successfully.');
    }


    public function historyPage()
    {
        $entryHistory = EntryHistory::all();
        $entryHistories = $entryHistory->map(function ($entry) {
            $data = json_decode($entry->data, true);
            return [
                'entry' => $data['entry'] ?? null,
                'products' => $data['products'] ?? []
            ];
        });
        return view('admin.history', compact('entryHistories'));
    }
    public function getHistory($id)
    {
        $entryHistory = EntryHistory::find($id);
        $data = json_decode($entryHistory->data, true);
        $entry = $data['entry'];
        $products = $data['products'];
    }


    // Excelsheet download
    public function export(Request $request)
    {
        // $status = $request->input('inputState');
        return Excel::download(new AdminExport($request), "contracts_export.xlsx");
    }

    public function welcome()
    {
        $items = Products::all();
        return view('welcome');
    }

    public function calculate(Request $request)
    {
        $itemIds = $request->input('products', []);
        return redirect()->back()->with('success', 'Items processed successfully!');
    }

}
