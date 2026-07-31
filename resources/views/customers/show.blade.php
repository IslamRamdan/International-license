<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4" dir="rtl">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight">
                    {{ __('بيانات العميل') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">عرض كافة تفاصيل الطلب الخاص بـ {{ $customer->full_name }}.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('customers.edit', $customer) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 rounded-xl font-bold text-xs text-white shadow-lg shadow-indigo-600/30 transition-all duration-200">
                    <i class="bi bi-pencil text-base"></i>
                    <span>تعديل البيانات</span>
                </a>
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl font-medium text-xs text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                    <i class="bi bi-arrow-right text-base"></i>
                    <span>العودة للقائمة</span>
                </a>
            </div>
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

        $licenseLabels = [
            'A' => ['name' => 'دراجة نارية', 'icon' => 'bi-bicycle'],
            'B' => ['name' => 'سيارة ركاب', 'icon' => 'bi-car-front-fill'],
            'C' => ['name' => 'مركبة نِقل', 'icon' => 'bi-truck'],
            'D' => ['name' => 'حافلة / باص', 'icon' => 'bi-bus-front-fill'],
        ];

        // مصفوفة تحويل قيمة مدة الرخصة إلى نص عربي مقروء
        $durationLabels = [
            '1' => 'سنة واحدة',
            '2' => 'سنتان',
            '3' => '3 سنوات',
            '5' => '5 سنوات',
            '10' => '10 سنوات',
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

        $licenseColors = [
            'A' => 'bg-gray-100 text-gray-700 ring-1 ring-gray-200',
            'B' => 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200',
            'C' => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',
            'D' => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200',
        ];
    @endphp

    <div class="py-10 bg-gray-50 min-h-screen form-shell" dir="rtl" x-data="{ lightbox: null }">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- رسالة النجاح --}}
            @if (session('success'))
                <div
                    class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-2 font-bold text-emerald-700">
                    <i class="bi bi-check-circle-fill text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- بطاقة الملخص العلوية -->
            <div class="bg-white overflow-hidden shadow-xl shadow-gray-200/60 rounded-3xl border border-gray-100">
                <div class="p-6 sm:p-8 flex flex-wrap items-center gap-5">
                    <div
                        class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-2xl shrink-0">
                        {{ mb_substr($customer->full_name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <h3 class="text-xl font-bold text-gray-900" dir="ltr">{{ $customer->full_name }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">رقم الطلب #{{ $customer->id }}</p>
                    </div>

                    <span
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold {{ $customer->gender === 'female' ? 'bg-pink-50 text-pink-700 ring-1 ring-pink-200' : 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' }}">
                        <i class="bi {{ $customer->gender === 'female' ? 'bi-gender-female' : 'bi-gender-male' }}"></i>
                        {{ $customer->gender === 'female' ? 'أنثى' : 'ذكر' }}
                    </span>

                    <span
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold {{ $licenseColors[$customer->license_category] ?? 'bg-gray-100 text-gray-700 ring-1 ring-gray-200' }}">
                        <i
                            class="bi {{ $licenseLabels[$customer->license_category]['icon'] ?? 'bi-card-checklist' }}"></i>
                        Category {{ $customer->license_category }}
                    </span>
                </div>
            </div>

            <!-- بطاقة التفاصيل -->
            <div class="bg-white overflow-hidden shadow-xl shadow-gray-200/60 rounded-3xl border border-gray-100">
                <div class="p-6 sm:p-10 text-gray-900 space-y-10">

                    <!-- القسم الأول: البيانات الشخصية -->
                    <div class="space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                            <span
                                class="step-badge w-9 h-9 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                                <i class="bi bi-person-badge text-lg"></i>
                            </span>
                            <h3 class="text-lg font-bold text-gray-900">البيانات الشخصية</h3>
                        </div>

                        <div class="flex flex-wrap gap-5">
                            <div class="flex-1 min-w-[200px]">
                                <span class="block text-xs font-semibold text-gray-500 mb-1">الاسم بالكامل</span>
                                <span class="block text-sm font-bold text-gray-900"
                                    dir="ltr">{{ $customer->full_name }}</span>
                            </div>
                            <div class="flex-1 min-w-[200px]">
                                <span class="block text-xs font-semibold text-gray-500 mb-1">تاريخ الميلاد</span>
                                <span class="block text-sm font-bold text-gray-900" dir="ltr">
                                    {{ $customer->birth_date }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- القسم الثاني: بيانات الهوية والإقامة -->
                    <div class="space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                            <span
                                class="step-badge w-9 h-9 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                                <i class="bi bi-globe-americas text-lg"></i>
                            </span>
                            <h3 class="text-lg font-bold text-gray-900">بيانات الهوية والإقامة</h3>
                        </div>

                        <div class="flex flex-wrap gap-5">
                            <div class="flex-1 min-w-[180px]">
                                <span class="block text-xs font-semibold text-gray-500 mb-1">فصيلة الدم</span>
                                <span
                                    class="inline-block px-2.5 py-1 rounded-lg text-sm font-bold bg-red-50 text-red-600"
                                    dir="ltr">{{ $customer->blood_type }}</span>
                            </div>
                            <div class="flex-1 min-w-[180px]">
                                <span class="block text-xs font-semibold text-gray-500 mb-1">رقم جواز السفر</span>
                                <span class="block text-sm font-bold text-gray-900 uppercase"
                                    dir="ltr">{{ $customer->passport_number }}</span>
                            </div>
                            <!-- حقل مدة الرخصة المضاف -->
                            <div class="flex-1 min-w-[180px]">
                                <span class="block text-xs font-semibold text-gray-500 mb-1">مدة الرخصة</span>
                                <span
                                    class="inline-block px-2.5 py-1 rounded-lg text-sm font-bold bg-indigo-50 text-indigo-700">
                                    {{ $durationLabels[$customer->license_duration] ?? $customer->license_duration . ' سنوات' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- القسم الرابع: المرفقات والمستندات -->
                    <div class="space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                            <span
                                class="step-badge w-9 h-9 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                                <i class="bi bi-paperclip text-lg"></i>
                            </span>
                            <h3 class="text-lg font-bold text-gray-900">المرفقات والمستندات</h3>
                        </div>

                        <div class="flex flex-wrap gap-5">
                            <!-- الصورة الشخصية -->
                            <div class="flex-1 min-w-[220px]">
                                <span class="block text-xs font-semibold text-gray-500 mb-2">الصورة الشخصية</span>
                                @if ($personalPhotoUrl)
                                    <div @click="lightbox = @js($personalPhotoUrl)"
                                        class="cursor-zoom-in group relative rounded-2xl overflow-hidden border border-gray-200">
                                        <img src="{{ $personalPhotoUrl }}"
                                            class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div
                                            class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                            <i
                                                class="bi bi-zoom-in text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="h-40 rounded-2xl border border-dashed border-gray-300 bg-gray-50/60 flex items-center justify-center text-gray-400 text-sm">
                                        <i class="bi bi-image ml-1"></i> لا توجد صورة
                                    </div>
                                @endif
                            </div>

                            <!-- رخصة القيادة -->
                            <div class="flex-1 min-w-[220px]">
                                <span class="block text-xs font-semibold text-gray-500 mb-2">رخصة القيادة المحلية</span>
                                @if ($localLicenseUrl)
                                    <div @click="lightbox = @js($localLicenseUrl)"
                                        class="cursor-zoom-in group relative rounded-2xl overflow-hidden border border-gray-200">
                                        <img src="{{ $localLicenseUrl }}"
                                            class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div
                                            class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                            <i
                                                class="bi bi-zoom-in text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="h-40 rounded-2xl border border-dashed border-gray-300 bg-gray-50/60 flex items-center justify-center text-gray-400 text-sm">
                                        <i class="bi bi-image ml-1"></i> لا توجد صورة
                                    </div>
                                @endif
                            </div>

                            <!-- جواز السفر -->
                            <div class="flex-1 min-w-[220px]">
                                <span class="block text-xs font-semibold text-gray-500 mb-2">صورة جواز السفر</span>
                                @if ($passportPhotoUrl)
                                    <div @click="lightbox = @js($passportPhotoUrl)"
                                        class="cursor-zoom-in group relative rounded-2xl overflow-hidden border border-gray-200">
                                        <img src="{{ $passportPhotoUrl }}"
                                            class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div
                                            class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                            <i
                                                class="bi bi-zoom-in text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="h-40 rounded-2xl border border-dashed border-gray-300 bg-gray-50/60 flex items-center justify-center text-gray-400 text-sm">
                                        <i class="bi bi-image ml-1"></i> لا توجد صورة
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Lightbox لتكبير الصور -->
        <div x-show="lightbox" x-cloak @click="lightbox = null"
            class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-6 cursor-zoom-out">
            <img :src="lightbox" class="max-w-full max-h-full rounded-2xl shadow-2xl" @click.stop>
            <button @click="lightbox = null"
                class="absolute top-5 left-5 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
    </div>
</x-app-layout>
