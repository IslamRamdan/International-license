<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
  /**
   * عرض جميع العملاء الخاصة بالمستخدم الحالي
   */
  public function dashboard()
  {
    $customers = auth()->user()->customers()->latest()->paginate(10);

    return view('dashboard', compact('customers'));
  }

  /**
   * عرض صفحة إنشاء عميل جديد
   */
  public function create()
  {
    return view('customers.create');
  }

  /**
   * حفظ بيانات عميل جديد
   */
  public function store(Request $request)
  {
    $validated = $this->validateCustomer($request);

    // رفع الصور
    $personalPath = $request->file('personal_photo')->store('documents/personal', 'public');
    $licensePath = $request->file('local_license')->store('documents/licenses', 'public');
    $licenseBackPath = $request->file('local_license_back')->store('documents/licenses', 'public');
    $passportPath = $request->file('passport_photo')->store('documents/passports', 'public');

    $birthDate = sprintf(
      '%04d-%02d-%02d',
      $request->birth_year,
      $request->birth_month,
      $request->birth_day
    );

    Customer::create(array_merge($validated, [
      'user_id'             => auth()->id(),
      'birth_date'          => $birthDate,
      'personal_photo'      => $personalPath,
      'local_license'       => $licensePath,
      'local_license_back'  => $licenseBackPath,
      'passport_photo'      => $passportPath,
    ]));

    return redirect()->route('dashboard')->with('success', 'تم إنشاء بيانات العميل بنجاح!');
  }

  /**
   * عرض تفاصيل عميل محدد
   */
  public function show(Customer $customer)
  {
    $this->authorizeUser($customer);

    return view('customers.show', compact('customer'));
  }

  /**
   * عرض صفحة تعديل عميل
   */
  public function edit(Customer $customer)
  {
    $this->authorizeUser($customer);

    // dd($customer); // Debugging line to inspect the $customer object

    return view('customers.edit', compact('customer'));
  }

  /**
   * تحديث بيانات عميل محدد
   */
  public function update(Request $request, Customer $customer)
  {
    $this->authorizeUser($customer);

    $validated = $this->validateCustomer($request, true);

    // تحديث الصورة الشخصية
    if ($request->hasFile('personal_photo')) {
      Storage::disk('public')->delete($customer->personal_photo);

      $customer->personal_photo = $request->file('personal_photo')
        ->store('documents/personal', 'public');
    }

    // تحديث صورة الرخصة الأمامية
    if ($request->hasFile('local_license')) {
      Storage::disk('public')->delete($customer->local_license);

      $customer->local_license = $request->file('local_license')
        ->store('documents/licenses', 'public');
    }

    // تحديث صورة الرخصة الخلفية
    if ($request->hasFile('local_license_back')) {
      Storage::disk('public')->delete($customer->local_license_back);

      $customer->local_license_back = $request->file('local_license_back')
        ->store('documents/licenses', 'public');
    }

    // تحديث صورة الجواز
    if ($request->hasFile('passport_photo')) {
      Storage::disk('public')->delete($customer->passport_photo);

      $customer->passport_photo = $request->file('passport_photo')
        ->store('documents/passports', 'public');
    }

    $birthDate = sprintf(
      '%04d-%02d-%02d',
      $request->birth_year,
      $request->birth_month,
      $request->birth_day
    );

    $customer->update(array_merge($validated, [
      'birth_date' => $birthDate,
    ]));

    return redirect()->route('dashboard')->with('success', 'تم تعديل البيانات بنجاح!');
  }

  /**
   * التحقق من صحة البيانات
   */
  private function validateCustomer(Request $request, bool $isUpdate = false): array
  {
    $imageRule = $isUpdate
      ? 'nullable|image|mimes:jpg,jpeg,png|max:2048'
      : 'required|image|mimes:jpg,jpeg,png|max:2048';

    return $request->validate([
      'full_name'            => 'required|string|regex:/^[a-zA-Z\s]+$/|max:255',
      'birth_day'            => 'required|numeric|between:1,31',
      'birth_month'          => 'required|numeric|between:1,12',
      'birth_year'           => 'required|numeric|max:' . (date('Y') - 18),
      'blood_type'           => 'required|string',
      'license_duration'     => 'required|integer|min:1',
      'passport_number'      => 'required|string',

      'personal_photo'       => $imageRule,
      'local_license'        => $imageRule,
      'local_license_back'   => $imageRule,
      'passport_photo'       => $imageRule,

      'terms'                => $isUpdate ? 'nullable' : 'accepted',
    ]);
  }

  /**
   * التأكد من ملكية المستخدم للبيانات
   */
  private function authorizeUser(Customer $customer): void
  {
    if ($customer->user_id !== auth()->id()) {
      abort(403, 'غير مسموح لك بالوصول لهذه البيانات');
    }
  }

  /**
   * تغيير حالة العميل
   */
  public function toggleStatus(Customer $customer)
  {
    $customer->status = 'completed';
    $customer->save();

    return response()->json([
      'success' => true,
      'status' => $customer->status,
      'message' => 'تم تحديث الحالة بنجاح',
    ]);
  }
}
