<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4" dir="rtl">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight">
                    {{ __('تعديل بيانات العميل') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">قم بتحديث بيانات العميل ({{ $customer->full_name ?? '' }}) وحفظ
                    التغييرات.</p>
            </div>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl font-medium text-xs text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                <i class="bi bi-arrow-right text-base"></i>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </x-slot>

    <!-- CDN لأيقونات Bootstrap إذا لم تكن مدمجة بالمشروع -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }

        .form-shell {
            font-family: 'Cairo', ui-sans-serif, system-ui, sans-serif;
        }

        .step-badge {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        }
    </style>

    @php
        // قائمة موسّعة بمعظم دول العالم (بالعربية) لاستخدامها في قوائم الجنسية / بلد الإصدار / بلد الإقامة
        $countries = [
            'EG' => 'مصر',
            'SA' => 'السعودية',
            'AE' => 'الإمارات',
            'KW' => 'الكويت',
            'QA' => 'قطر',
            'BH' => 'البحرين',
            'OM' => 'عُمان',
            'JO' => 'الأردن',
            'LB' => 'لبنان',
            'SY' => 'سوريا',
            'IQ' => 'العراق',
            'PS' => 'فلسطين',
            'YE' => 'اليمن',
            'LY' => 'ليبيا',
            'TN' => 'تونس',
            'DZ' => 'الجزائر',
            'MA' => 'المغرب',
            'SD' => 'السودان',
            'SO' => 'الصومال',
            'DJ' => 'جيبوتي',
            'MR' => 'موريتانيا',
            'KM' => 'جزر القمر',
            'TR' => 'تركيا',
            'IR' => 'إيران',
            'PK' => 'باكستان',
            'IN' => 'الهند',
            'BD' => 'بنغلاديش',
            'ID' => 'إندونيسيا',
            'MY' => 'ماليزيا',
            'PH' => 'الفلبين',
            'TH' => 'تايلاند',
            'VN' => 'فيتنام',
            'CN' => 'الصين',
            'JP' => 'اليابان',
            'KR' => 'كوريا الجنوبية',
            'AF' => 'أفغانستان',
            'LK' => 'سريلانكا',
            'NP' => 'نيبال',
            'UZ' => 'أوزبكستان',
            'KZ' => 'كازاخستان',
            'AZ' => 'أذربيجان',
            'GE' => 'جورجيا',
            'SG' => 'سنغافورة',
            'AM' => 'أرمينيا',
            'GB' => 'المملكة المتحدة',
            'FR' => 'فرنسا',
            'DE' => 'ألمانيا',
            'IT' => 'إيطاليا',
            'ES' => 'إسبانيا',
            'PT' => 'البرتغال',
            'NL' => 'هولندا',
            'BE' => 'بلجيكا',
            'CH' => 'سويسرا',
            'AT' => 'النمسا',
            'SE' => 'السويد',
            'NO' => 'النرويج',
            'DK' => 'الدنمارك',
            'FI' => 'فنلندا',
            'IE' => 'أيرلندا',
            'PL' => 'بولندا',
            'GR' => 'اليونان',
            'CZ' => 'التشيك',
            'RO' => 'رومانيا',
            'HU' => 'المجر',
            'RU' => 'روسيا',
            'UA' => 'أوكرانيا',
            'CY' => 'قبرص',
            'BG' => 'بلغاريا',
            'HR' => 'كرواتيا',
            'US' => 'الولايات المتحدة',
            'CA' => 'كندا',
            'MX' => 'المكسيك',
            'BR' => 'البرازيل',
            'AR' => 'الأرجنتين',
            'CL' => 'تشيلي',
            'CO' => 'كولومبيا',
            'PE' => 'بيرو',
            'AU' => 'أستراليا',
            'NZ' => 'نيوزيلندا',
            'ZA' => 'جنوب أفريقيا',
            'NG' => 'نيجيريا',
            'KE' => 'كينيا',
            'ET' => 'إثيوبيا',
            'GH' => 'غانا',
            'SN' => 'السنغال',
            'TZ' => 'تنزانيا',
            'UG' => 'أوغندا',
            'OT' => 'دولة أخرى',
        ];

        $personalPhotoUrl = $customer->personal_photo
            ? \Illuminate\Support\Facades\Storage::url($customer->personal_photo)
            : null;
        $localLicenseUrl = $customer->local_license
            ? \Illuminate\Support\Facades\Storage::url($customer->local_license)
            : null;
        $passportPhotoUrl = $customer->passport_photo
            ? \Illuminate\Support\Facades\Storage::url($customer->passport_photo)
            : null;
    @endphp

    <div class="py-10 bg-gray-50 min-h-screen form-shell" dir="rtl" x-data="{
        gender: '{{ old('gender', $customer->gender) }}',
        license: '{{ old('license_category', $customer->license_category) }}'
    }">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white overflow-hidden shadow-xl shadow-gray-200/60 sm:rounded-3xl border border-gray-100 transition-all">
                <div class="p-6 sm:p-10 text-gray-900">

                    {{-- عرض أخطاء الـ Validation --}}
                    @if ($errors->any())
                        <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-2xl">
                            <div class="flex items-center gap-2 font-bold text-red-700 mb-2">
                                <i class="bi bi-exclamation-triangle-fill text-lg"></i>
                                <span>يرجى تصحيح الأخطاء التالية:</span>
                            </div>
                            <ul class="list-disc list-inside text-sm text-red-600 space-y-1 pr-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- رسالة نجاح --}}
                    @if (session('success'))
                        <div
                            class="mb-8 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-2 font-bold text-emerald-700">
                            <i class="bi bi-check-circle-fill text-lg"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('customers.update', $customer) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-10">
                        @csrf
                        @method('PUT')

                        <!-- القسم الأول: البيانات الشخصية -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                                <span
                                    class="step-badge w-9 h-9 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                                    <i class="bi bi-person-badge text-lg"></i>
                                </span>
                                <h3 class="text-lg font-bold text-gray-900">البيانات الشخصية</h3>
                            </div>

                            <!-- الاسم بالكامل -->
                            <div>
                                <x-input-label for="full_name" class="font-semibold text-gray-700">
                                    {{ __('الاسم بالكامل (بالإنجليزية)') }} <span class="text-red-500">*</span>
                                </x-input-label>
                                <div class="mt-1.5 relative rounded-xl shadow-sm">
                                    <x-text-input id="full_name" name="full_name" type="text"
                                        class="block w-full text-left rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-gray-50/60 hover:bg-white text-gray-900 transition-all py-3 px-4"
                                        dir="ltr" :value="old('full_name', $customer->full_name)" placeholder="e.g. John Doe" required />
                                </div>
                                <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                                    <i class="bi bi-info-circle"></i>
                                    <span>حروف إنجليزية فقط - مطابقة للاسم كما هو في جواز السفر (أقصى حد 25 حرف).</span>
                                </p>
                            </div>

                            <!-- تاريخ الميلاد -->
                            @php
                                $birthDate = $customer->birth_date
                                    ? \Carbon\Carbon::parse($customer->birth_date)
                                    : null;
                                $defaultDay = $birthDate ? $birthDate->format('d') : '';
                                $defaultMonth = $birthDate ? $birthDate->format('m') : '';
                                $defaultYear = $birthDate ? $birthDate->format('Y') : '';
                            @endphp

                            <div class="flex-1">
                                <label class="block font-semibold text-sm text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('تاريخ الميلاد') }} <span class="text-red-500">*</span>
                                </label>

                                <div class="flex gap-3" dir="ltr">
                                    <input name="birth_day" type="number"
                                        class="clean-input flex-1 block w-full text-center rounded-xl border-gray-200 bg-gray-50/60 hover:bg-white text-gray-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 py-2.5 transition-all"
                                        placeholder="DD" min="1" max="31"
                                        value="{{ old('birth_day', $defaultDay) }}" required />

                                    <input name="birth_month" type="number"
                                        class="clean-input flex-1 block w-full text-center rounded-xl border-gray-200 bg-gray-50/60 hover:bg-white text-gray-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 py-2.5 transition-all"
                                        placeholder="MM" min="1" max="12"
                                        value="{{ old('birth_month', $defaultMonth) }}" required />

                                    <input name="birth_year" type="number"
                                        class="clean-input flex-1 block w-full text-center rounded-xl border-gray-200 bg-gray-50/60 hover:bg-white text-gray-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 py-2.5 transition-all"
                                        placeholder="YYYY" min="1930" max="{{ date('Y') - 18 }}"
                                        value="{{ old('birth_year', $defaultYear) }}" required />
                                </div>
                                <p class="text-xs text-gray-500 mt-2 text-right">أكبر من 18 عامًا.</p>
                            </div>
                        </div>

                        <!-- القسم الثاني: بيانات الإقامة والجنسية -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                                <span
                                    class="step-badge w-9 h-9 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                                    <i class="bi bi-globe-americas text-lg"></i>
                                </span>
                                <h3 class="text-lg font-bold text-gray-900">بيانات الهوية والإقامة</h3>
                            </div>

                            <div class="flex flex-wrap gap-5">
                                <!-- فصيلة الدم -->
                                <div class="flex-1 min-w-[200px]">
                                    <x-input-label for="blood_type" class="font-semibold text-gray-700">
                                        {{ __('فصيلة الدم') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <select id="blood_type" name="blood_type"
                                        class="mt-1.5 block w-full border-gray-200 bg-gray-50/60 hover:bg-white text-gray-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl shadow-sm py-3 px-4 transition-all"
                                        required>
                                        <option value="" disabled
                                            {{ old('blood_type', $customer->blood_type) ? '' : 'selected' }}>اختر فصيلة
                                            الدم</option>
                                        @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $type)
                                            <option value="{{ $type }}"
                                                {{ old('blood_type', $customer->blood_type) == $type ? 'selected' : '' }}>
                                                {{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- رقم جواز السفر -->
                                <div class="flex-1 min-w-[200px]">
                                    <x-input-label for="passport_number" class="font-semibold text-gray-700">
                                        {{ __('رقم جواز السفر') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input id="passport_number" name="passport_number" type="text"
                                        class="mt-1.5 block w-full rounded-xl py-3 uppercase border-gray-200 bg-gray-50/60 hover:bg-white text-gray-900 focus:ring-4 focus:ring-indigo-500/10"
                                        :value="old('passport_number', $customer->passport_number)" placeholder="A12345678" required />
                                </div>

                                <!-- مدة الرخصة (تمت إضافتها هنا) -->
                                <div class="flex-1 min-w-[200px]">
                                    <x-input-label for="license_duration" class="font-semibold text-gray-700">
                                        {{ __('مدة الرخصة') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <select id="license_duration" name="license_duration"
                                        class="mt-1.5 block w-full border-gray-200 bg-gray-50/60 hover:bg-white text-gray-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-xl shadow-sm py-3 px-4 transition-all"
                                        required>
                                        <option value="" disabled
                                            {{ old('license_duration', $customer->license_duration ?? '') ? '' : 'selected' }}>
                                            اختر مدة الرخصة</option>
                                        <option value="1"
                                            {{ old('license_duration', $customer->license_duration ?? '') == '1' ? 'selected' : '' }}>
                                            سنة واحدة</option>
                                        <option value="2"
                                            {{ old('license_duration', $customer->license_duration ?? '') == '2' ? 'selected' : '' }}>
                                            سنتان</option>
                                        <option value="3"
                                            {{ old('license_duration', $customer->license_duration ?? '') == '3' ? 'selected' : '' }}>
                                            3 سنوات</option>
                                        <option value="5"
                                            {{ old('license_duration', $customer->license_duration ?? '') == '5' ? 'selected' : '' }}>
                                            5 سنوات</option>
                                        <option value="10"
                                            {{ old('license_duration', $customer->license_duration ?? '') == '10' ? 'selected' : '' }}>
                                            10 سنوات</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- القسم الرابع: المرفقات والمستندات -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                                <span
                                    class="step-badge w-9 h-9 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                                    <i class="bi bi-paperclip text-lg"></i>
                                </span>
                                <h3 class="text-lg font-bold text-gray-900">المرفقات والمستندات</h3>
                            </div>
                            <p class="text-xs text-gray-500 -mt-4 flex items-center gap-1">
                                <i class="bi bi-info-circle"></i>
                                <span>الصور الحالية معروضة تحت كل حقل. ارفع صورة جديدة فقط لو حابب تستبدلها.</span>
                            </p>

                            <div class="flex flex-wrap gap-5">
                                <!-- الصورة الشخصية -->
                                <div x-data="{ preview: @js($personalPhotoUrl) }"
                                    class="flex-1 min-w-[220px] p-4 border border-dashed border-gray-300 rounded-2xl bg-gray-50/60 hover:bg-white hover:border-indigo-300 transition-all">
                                    <x-input-label for="personal_photo"
                                        class="font-semibold text-gray-700 mb-2 block">
                                        {{ __('الصورة الشخصية') }}
                                    </x-input-label>
                                    <input type="file" id="personal_photo" name="personal_photo" accept="image/*"
                                        @change="preview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : @js($personalPhotoUrl)"
                                        class="block w-full text-xs text-gray-500 file:mr-0 file:ml-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all cursor-pointer">
                                    <div x-show="preview" x-cloak class="mt-3 relative">
                                        <img :src="preview"
                                            class="w-full h-32 object-cover rounded-xl border border-gray-200 shadow-sm">
                                        <span
                                            class="absolute top-1.5 left-1.5 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                                            <i class="bi bi-check-lg"></i> الصورة الحالية
                                        </span>
                                    </div>
                                </div>

                                <!-- رخصة القيادة -->
                                <div x-data="{ preview: @js($localLicenseUrl) }"
                                    class="flex-1 min-w-[220px] p-4 border border-dashed border-gray-300 rounded-2xl bg-gray-50/60 hover:bg-white hover:border-indigo-300 transition-all">
                                    <x-input-label for="local_license" class="font-semibold text-gray-700 mb-2 block">
                                        {{ __('رخصة القيادة المحلية') }}
                                    </x-input-label>
                                    <input type="file" id="local_license" name="local_license" accept="image/*"
                                        @change="preview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : @js($localLicenseUrl)"
                                        class="block w-full text-xs text-gray-500 file:mr-0 file:ml-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all cursor-pointer">
                                    <div x-show="preview" x-cloak class="mt-3 relative">
                                        <img :src="preview"
                                            class="w-full h-32 object-cover rounded-xl border border-gray-200 shadow-sm">
                                        <span
                                            class="absolute top-1.5 left-1.5 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                                            <i class="bi bi-check-lg"></i> الصورة الحالية
                                        </span>
                                    </div>
                                </div>

                                <!-- جواز السفر -->
                                <div x-data="{ preview: @js($passportPhotoUrl) }"
                                    class="flex-1 min-w-[220px] p-4 border border-dashed border-gray-300 rounded-2xl bg-gray-50/60 hover:bg-white hover:border-indigo-300 transition-all">
                                    <x-input-label for="passport_photo"
                                        class="font-semibold text-gray-700 mb-2 block">
                                        {{ __('صورة جواز السفر') }}
                                    </x-input-label>
                                    <input type="file" id="passport_photo" name="passport_photo" accept="image/*"
                                        @change="preview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : @js($passportPhotoUrl)"
                                        class="block w-full text-xs text-gray-500 file:mr-0 file:ml-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all cursor-pointer">
                                    <div x-show="preview" x-cloak class="mt-3 relative">
                                        <img :src="preview"
                                            class="w-full h-32 object-cover rounded-xl border border-gray-200 shadow-sm">
                                        <span
                                            class="absolute top-1.5 left-1.5 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                                            <i class="bi bi-check-lg"></i> الصورة الحالية
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الإقرار والشروط -->
                        <div>
                            <div
                                class="p-4 rounded-2xl border border-amber-200/80 bg-amber-50/60 flex items-start gap-3">
                                <input id="terms" type="checkbox" name="terms"
                                    class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer"
                                    {{ old('terms', true) ? 'checked' : '' }} required>
                                <label for="terms"
                                    class="text-xs text-amber-900 leading-relaxed cursor-pointer select-none">
                                    أقر بـأن الصورة الشخصية بخلفية بيضاء، وجواز السفر ساري المفعول، ورخصة القيادة
                                    المحلية صالحة، وأن كافة البيانات والأوراق المدخلة أصلية وصحيحة تمامًا وتحت مسؤوليتي
                                    الشخصية.
                                </label>
                            </div>
                        </div>

                        <!-- أزرار الإرسال -->
                        <div class="flex flex-wrap gap-3">
                            <button type="submit"
                                class="flex-1 min-w-[200px] inline-flex justify-center items-center gap-2 py-3.5 px-6 bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500/20 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition-all duration-200">
                                <span>حفظ التعديلات</span>
                                <i class="bi bi-check2-circle text-lg"></i>
                            </button>
                            <a href="{{ route('dashboard') }}"
                                class="flex-1 min-w-[150px] inline-flex justify-center items-center gap-2 py-3.5 px-6 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold rounded-xl transition-all duration-200">
                                <span>إلغاء</span>
                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
