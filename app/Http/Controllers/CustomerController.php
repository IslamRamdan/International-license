<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
    // $licenseBackPath = $request->file('local_license_back')->store('documents/licenses', 'public');
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
      // 'local_license_back'  => $licenseBackPath,
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
  private function validateCustomer(Request $request, bool $isUpdate = false, $customerId = null): array
  {
    $imageRule = $isUpdate
      ? 'nullable|image|mimes:jpg,jpeg,png'
      : 'required|image|mimes:jpg,jpeg,png';

    // الفحص بشرط عدم التكرار لنفس الـ user_id
    $passportRule = Rule::unique('customers', 'passport_number')
      ->where(function ($query) {
        return $query->where('user_id', auth()->id());
      });

    if ($isUpdate && $customerId) {
      $passportRule->ignore($customerId);
    }

    $rules = [
      'full_name'        => 'required|string|regex:/^[a-zA-Z\s]+$/|max:255',
      'birth_day'        => 'required|numeric|between:1,31',
      'birth_month'      => 'required|numeric|between:1,12',
      'birth_year'       => 'required|numeric|max:' . (date('Y') - 18),
      'blood_type'       => 'required|string',
      'license_duration' => 'required|integer|min:1',
      'passport_number'  => ['required', 'string', $passportRule],
      'personal_photo'   => $imageRule,
      'local_license'    => $imageRule,
      // 'local_license_back' => $imageRule,
      'passport_photo'   => $imageRule,
    ];

    // رسائل الخطأ المخصصة باللغة العربية
    $messages = [
      'full_name.required'        => 'الاسم بالكامل مطلوب.',
      'full_name.string'          => 'الاسم يجب أن يكون نصاً.',
      'full_name.regex'           => 'الاسم يجب أن يحتوي على أحرف إنجليزية فقط.',
      'full_name.max'             => 'الاسم يجب ألا يتجاوز 255 حرفاً.',

      'birth_day.required'        => 'يوم الميلاد مطلوب.',
      'birth_day.numeric'         => 'يوم الميلاد يجب أن يكون رقماً.',
      'birth_day.between'         => 'يوم الميلاد يجب أن يكون بين 1 و 31.',

      'birth_month.required'      => 'شهر الميلاد مطلوب.',
      'birth_month.numeric'       => 'شهر الميلاد يجب أن يكون رقماً.',
      'birth_month.between'       => 'شهر الميلاد يجب أن يكون بين 1 و 12.',

      'birth_year.required'       => 'سنة الميلاد مطلوبة.',
      'birth_year.numeric'        => 'سنة الميلاد يجب أن تكون رقماً.',
      'birth_year.max'            => 'يجب أن يكون عمر العميل 18 سنة على الأقل.',

      'blood_type.required'       => 'فصيلة الدم مطلوبة.',
      'license_duration.required' => 'مدة الرخصة مطلوبة.',
      'license_duration.integer'  => 'مدة الرخصة يجب أن تكون رقماً صحيحاً.',
      'license_duration.min'      => 'مدة الرخصة يجب أن تكون سنة واحدة على الأقل.',

      'passport_number.required'  => 'رقم جواز السفر مطلوب.',
      'passport_number.unique'    => 'رقم جواز السفر هذا مسجل لديك بالفعل.',

      'personal_photo.required'   => 'الصورة الشخصية مطلوبة.',
      'personal_photo.image'      => 'الصورة الشخصية يجب أن تكون ملف صورة.',
      'personal_photo.mimes'      => 'الصورة الشخصية يجب أن تكون بصيغة (jpg, jpeg, png).',
      'personal_photo.max'        => 'حجم الصورة الشخصية يجب ألا يتجاوز 2 ميجابايت.',

      'local_license.required'    => 'صورة الرخصة المحلية مطلوبة.',
      'local_license.image'       => 'صورة الرخصة المحلية يجب أن تكون ملف صورة.',
      'local_license.mimes'       => 'صورة الرخصة المحلية يجب أن تكون بصيغة (jpg, jpeg, png).',
      'local_license.max'         => 'حجم صورة الرخصة المحلية يجب ألا يتجاوز 2 ميجابايت.',

      'passport_photo.required'   => 'صورة جواز السفر مطلوبة.',
      'passport_photo.image'      => 'صورة جواز السفر يجب أن تكون ملف صورة.',
      'passport_photo.mimes'      => 'صورة جواز السفر يجب أن تكون بصيغة (jpg, jpeg, png).',
      'passport_photo.max'        => 'حجم صورة جواز السفر يجب ألا يتجاوز 2 ميجابايت.',
    ];

    return $request->validate($rules, $messages);
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
  public function toAdmin(Customer $customer)
  {
    # code...
    $customer->status = 'admin';
    $customer->save();

    return response()->json([
      'success' => true,
      'status' => $customer->status,
      'message' => 'تم تحديث الحالة بنجاح',
    ]);
  }
}
