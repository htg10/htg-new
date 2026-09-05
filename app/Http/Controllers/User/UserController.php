<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Telecaller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Http\Controllers\Admin\SMSController;
use App\Mail\ConfirmMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Entry;
use App\Models\Products;
use App\Models\EntryHistory;
use App\Mail\SendMail;
use App\Mail\RenewalEmail;
use Carbon\Carbon;
use DB;
use ZipArchive;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = Entry::with('product')->where('user_id', Auth::id());

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
                // $query->where('expiry_date', '<', now());
                $query->whereHas('product', function ($query) {
                    $query->where('expiry_date', '<', now());
                });
            } elseif ($request->inputState === 'Pending') {
                $query->whereHas('product', function ($query) {
                    $query->whereRaw('total_amount - paid_amount > 0');
                });
            }
        }

        $entries = $query->latest()->paginate(10);

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
        // dd($entry->product->total_amount);

        return view('user.contract.index', compact('entries', 'totalAmount', 'totalBalance'));
    }

    // public function assignedLeads()
    // {
    //     $leads = Telecaller::where('user_id', auth()->id())
    //         ->where('status', 'NEW')
    //         ->latest()
    //         ->paginate(10);

    //     return view('user.leads.index', compact('leads'));
    // }

    public function assignedLeads(Request $request)
    {
        $leads = Telecaller::where('user_id', auth()->id())
            ->where('status', 'NEW');

        // Business filter
        if ($request->filled('business')) {
            $leads->where('business', 'like', '%' . $request->business . '%');
        }

        // Customer Name filter
        if ($request->filled('name')) {
            $leads->where('name', 'like', '%' . $request->name . '%');
        }

        // Interest filter
        if ($request->filled('interest')) {
            $leads->where('interest', $request->interest);
        }

        // Deal Status filter
        if ($request->filled('deal_status')) {
            $leads->where('deal_status', $request->deal_status);
        }

        // Date range filter (meeting date)
        if ($request->filled('from_date')) {
            $leads->whereDate('meeting_datetime', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $leads->whereDate('meeting_datetime', '<=', $request->to_date);
        }

        $leads = $leads->latest()->get();

        return view('user.leads.index', compact('leads'));
    }

    public function createFromLead($id)
    {
        $lead = Telecaller::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        $users = User::where('role_id', 2)->get();
        $banks = Bank::all();

        return view('user.contract.addnew-from-lead', compact('lead', 'users', 'banks'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'deal_status' => 'required|in:pending,follow up,deal closed,not interested',
            'follow_up_date' => 'nullable|date',
            'follow_up_remark' => 'nullable',
        ]);

        $data = [
            'deal_status' => $request->deal_status,
            'follow_up_remark' => $request->follow_up_remark,
        ];

        if ($request->deal_status === 'follow up') {
            $data['follow_up_date'] = $request->follow_up_date;
        } else {
            $data['follow_up_date'] = null;
        }

        // Telecaller::where('id', $id)
        //     ->where('user_id', auth()->id())
        //     ->update([
        //         'deal_status' => $request->deal_status
        //     ]);
        Telecaller::where('id', $id)
            ->where('user_id', auth()->id())
            ->update($data);

        return back()->with('success', 'Status updated successfully');
    }


    public function addNew()
    {
        $users = User::where('role_id', 2)->get();
        $banks = Bank::all();
        return view('user.contract.addnew', compact('users', 'banks'));
    }


    public function renew()
    {
        $users = User::where('role_id', 2)->get();
        $banks = Bank::all();
        return view('user.contract.renew', compact('users', 'banks'));
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
            'telecaller_id' => 'nullable|exists:telecallers,id',
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

        $entry = Entry::create($data);
        $startDate = Carbon::now();

        foreach ($products as $product) {
            if (isset($product['name'])) {

                $validity = $product['validity'];
                $expiryDate = null;

                switch ($validity) {
                    case '1 Month':
                        $expiryDate = $startDate->addMonth()->format('Y-m-d');
                        break;
                    case '3 Months':
                        $expiryDate = $startDate->addMonths(3)->format('Y-m-d');
                        break;
                    case '4 Months':
                        $expiryDate = $startDate->addMonths(4)->format('Y-m-d');
                        break;
                    case '6 Months':
                        $expiryDate = $startDate->addMonths(6)->format('Y-m-d');
                        break;
                    case '12 Months':
                        $expiryDate = $startDate->addYear()->format('Y-m-d');
                        break;
                    case '24 Months':
                        $expiryDate = $startDate->addMonths(24)->format('Y-m-d');
                        break;
                    case '36 Months':
                        $expiryDate = $startDate->addMonths(36)->format('Y-m-d');
                        break;
                    case 'Lifetime':
                        $expiryDate = $startDate->addYears(100)->format('Y-m-d');
                        break;
                    default:
                        break;
                }

                Products::create([
                    'product_name' => $product['name'],
                    'total_amount' => $product['total_amount'],
                    'paid_amount' => $product['paid_amount'],
                    'balance_amount' => $product['total_amount'] - $product['paid_amount'],
                    'validity' => $validity,
                    'expiry_date' => $expiryDate,
                    'entry_id' => $entry->id,
                ]);

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


        return redirect('/user/index')->with('success', 'Add successfully.');
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
        return view('user.contract.edit', compact('entries', 'products', 'totalAmount', 'totalBalance', 'totalPayment', 'users', 'banks'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // validation rules
        ]);

        $entry = Entry::findOrFail($id);
        $data = $request->all();

        $existingProducts = Products::where('entry_id', $entry->id)->get();

        // EntryHistory::create([
        //     'original_entry_id' => $entry->id,
        //     'data' => json_encode([
        //         'entry' => $entry->toArray(),
        //         'products' => $existingProducts->toArray(),
        //     ]),
        // ]);

        $products = $request->input('products', []);
        $data = $request->except('products');

        if ($request->hasFile('image')) {
            $oldImages = json_decode($entry->image);

            if (!empty($oldImages)) {
                foreach ($oldImages as $oldImagePath) {
                    $oldImageFullPath = public_path($oldImagePath);
                    if (file_exists($oldImageFullPath)) {
                        unlink($oldImageFullPath);
                    }
                }
            }
        }


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

        $entry->update($data);

        $startDate = Carbon::now();

        foreach ($products as $product) {
            if (isset($product['name'])) {

                $validity = $product['validity'];
                $expiryDate = null;

                switch ($validity) {
                    case '1 Month':
                        $expiryDate = $startDate->addMonth()->format('Y-m-d');
                        break;
                    case '3 Months':
                        $expiryDate = $startDate->addMonths(3)->format('Y-m-d');
                        break;
                    case '4 Months':
                        $expiryDate = $startDate->addMonths(4)->format('Y-m-d');
                        break;
                    case '6 Months':
                        $expiryDate = $startDate->addMonths(6)->format('Y-m-d');
                        break;
                    case '12 Months':
                        $expiryDate = $startDate->addYear()->format('Y-m-d');
                        break;
                    case '24 Months':
                        $expiryDate = $startDate->addMonths(24)->format('Y-m-d');
                        break;
                    case '36 Months':
                        $expiryDate = $startDate->addMonths(36)->format('Y-m-d');
                        break;
                    case 'Lifetime':
                        $expiryDate = $startDate->addYears(100)->format('Y-m-d');
                        break;
                    default:
                        break;
                }
                $product_old = Products::where('entry_id', $entry->id)->where('product_name', $product['name'])->first();

                // dd($product_old);
                Products::updateOrCreate(
                    ['entry_id' => $entry->id, 'product_name' => $product['name']],
                    [
                        'total_amount' => $product['total_amount'],
                        'paid_amount' => $product_old->paid_amount + $product['paid_amount'],
                        'balance_amount' => $product_old->balance_amount - $product['paid_amount'],
                        'validity' => $validity,
                        'expiry_date' => $expiryDate,
                    ]
                );

                // for mail
                // $productDetails[] = [
                //     'product_name' => $product['name'],
                //     'total_amount' => $product['total_amount'],
                //     'balance_amount' => $product['balance_amount'],
                //     'validity' => $validity,
                //     'expiry_date' => $expiryDate
                // ];
            }
        }

        // dd($balanceAmountSum);

        // $sms_tmp_id = env('NEW_SERVICE_TEMPLATE_ID');
        // $msg = "Welcome to Help Together Group, " . $entry->contact . "! Thank you for choosing Services.";

        // SMSController::sendSms($sms_tmp_id, $msg, $entry->contactno);
        // Mail::to($data['email'])->send(new ConfirmMail($data, $productDetails));

        return redirect('/user/index')->with('success', 'Update successfully.');
    }

    function delete($id)
    {
        $entry = Entry::find($id);
        $entry->delete();
        return redirect('/user/index')->with('success', 'Delete successfully.');
    }

    // Excelsheet download
    public function export(Request $request)
    {
        $status = $request->input('inputState');
        $userId = Auth::id();
        return Excel::download(new UsersExport($status, $userId), "contracts_{$status}.xlsx");
    }

    public function calculate(Request $request)
    {
        $itemIds = $request->input('products', []);
        return redirect()->back()->with('success', 'Items processed successfully!');
    }

    // fore leads Create, edit and delete
    public function index1(Request $request)
    {
        $telecallers = Telecaller::where('created_by', Auth::id());

        if ($request->filled('business')) {
            $telecallers->where('business', 'like', '%' . $request->business . '%');
        }

        if ($request->filled('bdm_id')) {
            $telecallers->where('user_id', $request->bdm_id);
        }

        // Filter by interest
        if ($request->filled('interest')) {
            $telecallers->where('interest', 'like', '%' . $request->interest . '%');
        }

        // Filter by deal status
        if ($request->filled('deal_status')) {
            $telecallers->where('deal_status', $request->deal_status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $telecallers->whereBetween('created_at', [
                $request->from_date,
                $request->to_date
            ]);
        }

        $telecallers = $telecallers->latest()->get();
        $users = User::where('role_id', 2)->get();

        // $telecallers = Telecaller::latest()->paginate(10);
        return view('user.leads.show.index', compact('telecallers', 'users'));
    }

    public function create1()
    {
        $users = User::where('role_id', 2)->get();
        return view('user.leads.show.create', compact('users'));
    }

    public function store1(Request $request)
    {
        $validated = $request->validate([
            'business' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:15',
            'user_id' => 'required|exists:users,id',
            'meeting_datetime' => 'nullable|date',
            'interest' => 'required|string',
            'remark' => 'nullable|string|max:255',

            'products' => 'nullable|array',
            'products.*' => 'nullable|string|max:255',

            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_accuracy' => 'nullable|string|max:255',
            'location_url' => 'required|string',
        ]);

        Telecaller::create([
            'business' => $validated['business'],
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'mobile' => $validated['mobile'],
            'user_id' => $validated['user_id'],
            'created_by' => Auth::id(),
            'meeting_datetime' => $validated['meeting_datetime'] ?? null,
            'interest' => $validated['interest'],
            'remark' => $validated['remark'] ?? null,
            'products' => $validated['products'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'location_accuracy' => $validated['location_accuracy'] ?? null,
            'location_url' => $validated['location_url'] ?? null,
            'status' => 'NEW',
        ]);

        return redirect('/user/lead/index')->with('success', 'Add successfully.');
    }


    public function edit1($id)
    {
        $telecallers = Telecaller::findOrFail($id);
        $users = User::where('role_id', 2)->get();
        // dd($telecallers);
        return view('user.leads.show.edit', compact('users', 'telecallers'));
    }

    public function update1(Request $request, $id)
    {
        $validated = $request->validate([
            'business' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:15',
            'user_id' => 'required|exists:users,id',
            'meeting_datetime' => 'nullable|date',
            'interest' => 'required|string',
            'remark' => 'nullable|string|max:255',

            'products' => 'nullable|array',
            'products.*' => 'nullable|string|max:255',

            // 'latitude' => 'nullable|numeric',
            // 'longitude' => 'nullable|numeric',
            // 'location_accuracy' => 'nullable|string|max:255',
            // 'location_url' => 'nullable|string',
        ]);

        $telecaller = Telecaller::findOrFail($id);

        $telecaller->update([
            'business' => $validated['business'],
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'mobile' => $validated['mobile'],
            'user_id' => $validated['user_id'],
            'meeting_datetime' => $validated['meeting_datetime'] ?? null,
            'interest' => $validated['interest'],
            'remark' => $validated['remark'] ?? null,
            'products' => $validated['products'] ?? null,
            // 'latitude' => $validated['latitude'] ?? null,
            // 'longitude' => $validated['longitude'] ?? null,
            // 'location_accuracy' => $validated['location_accuracy'] ?? null,
            // 'location_url' => $validated['location_url'] ?? null,
        ]);

        return redirect('/user/lead/index')->with('success', 'Update successfully.');
    }

    function delete1($id)
    {
        $telecaller = Telecaller::find($id);
        $telecaller->delete();
        return redirect('/user/lead/index')->with('success', 'Delete successfully.');
    }
}
