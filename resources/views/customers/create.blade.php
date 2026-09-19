<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4" dir="rtl">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 :text-gray-100 tracking-tight">
                    {{ __('إضافة عميل جديد') }}
                </h2>
                <p class="text-sm text-gray-500 :text-gray-400 mt-1">قم بملء البيانات التالية لإصدار الطلب ورخصة القيادة
                    الدولية.</p>
            </div>

            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2 px-4 py-2 bg-white :bg-gray-800 border border-gray-300 :border-gray-600 rounded-md font-semibold text-sm text-gray-700 :text-gray-200 shadow-sm hover:bg-gray-50 :hover:bg-gray-700 transition-all duration-200">
                <i class="bi bi-arrow-right text-base"></i>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </x-slot>

    <!-- CDNs -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Cropper.js (اقتصاص وتدوير الصور) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .form-shell {
            font-family: 'Cairo', ui-sans-serif, system-ui, sans-serif;
        }

        /* Clean GitHub-inspired focus states */
        .clean-input:focus {
            box-shadow: 0 0 0 3px rgba(9, 105, 218, 0.3);
            border-color: #0969da;
            outline: none;
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
            'US' => 'الولايات المتحدة',
            'GB' => 'المملكة المتحدة',
            'CA' => 'كندا',
            'AU' => 'أستراليا',
            'OT' => 'دولة أخرى',
        ];

        // المرفقات المطلوبة (name = اسم الحقل في الفورم)
        $attachments = [
            ['name' => 'personal_photo', 'label' => 'الصورة الشخصية'],
            ['name' => 'local_license', 'label' => 'رخصة القيادة المحلية'],
            ['name' => 'passport_photo', 'label' => 'صورة جواز السفر'],
        ];
    @endphp

    <div class="py-10 bg-[#f6f8fa] min-h-screen form-shell" dir="rtl" x-data="{
        gender: '{{ old('gender') }}',
        license: '{{ old('license_category') }}',
        mrzInput: '',
        mrzError: '',
        mrzResult: null,
    
        parseMRZ() {
            this.mrzError = '';
            this.mrzResult = null;
    
            const lines = this.mrzInput
                .trim()
                .toUpperCase()
                .split(/\r?\n/)
                .map(l => l.replace(/\s/g, ''))
                .filter(Boolean);
    
            if (lines.length < 2) {
                this.mrzError = 'يرجى لصق سطرَي الـ MRZ كاملين (كل سطر يبدأ عادةً بـ P< أو رقم الجواز).';
                return;
            }
    
            const line1 = lines[lines.length - 2].padEnd(44, '<').slice(0, 44);
            const line2 = lines[lines.length - 1].padEnd(44, '<').slice(0, 44);
    
            // نتأكد إن فيه محتوى كافٍ فعلاً (مش مجرد سطر فاضي اتكمّل بـ <) قبل ما نكمل
            if (lines[lines.length - 2].length < 15 || lines[lines.length - 1].length < 28) {
                this.mrzError = 'أحد السطرين قصير جدًا، تأكد إنك لصقت السطرين كاملين من أسفل صفحة البيانات.';
                return;
            }
    
            try {
                // ---- السطر الأول: الاسم ----
                const namesRaw = line1.substring(5).split('<<');
                const surname = (namesRaw[0] || '').replace(/</g, ' ').trim();
                const givenNames = (namesRaw.slice(1).join(' ') || '').replace(/</g, ' ').trim();
                const fullName = [givenNames, surname].filter(Boolean).join(' ');
    
                // ---- السطر الثاني: باقي البيانات ----
                const passportNumber = line2.substring(0, 9).replace(/</g, '').trim();
                const nationality = line2.substring(10, 13).replace(/</g, '');
                const birthRaw = line2.substring(13, 19);
                const sex = line2.substring(20, 21);
                const expiryRaw = line2.substring(21, 27);
    
                // تاريخ الميلاد: ممكن يكون في القرن الماضي أو الحالي حسب السنة الحالية
                const toBirthDate = (raw) => {
                    const yy = parseInt(raw.substring(0, 2), 10);
                    const mm = parseInt(raw.substring(2, 4), 10);
                    const dd = parseInt(raw.substring(4, 6), 10);
                    const currentYY = new Date().getFullYear() % 100;
                    const year = (yy > currentYY ? 1900 : 2000) + yy;
                    return { day: dd, month: mm, year: year };
                };
    
                // تاريخ الانتهاء: دايمًا في القرن الحالي (20xx) لأن الجواز لا يكون منتهيًا من قرن ماضٍ
                const toExpiryDate = (raw) => {
                    const yy = parseInt(raw.substring(0, 2), 10);
                    const mm = parseInt(raw.substring(2, 4), 10);
                    const dd = parseInt(raw.substring(4, 6), 10);
                    return { day: dd, month: mm, year: 2000 + yy };
                };
    
                const birth = toBirthDate(birthRaw);
                const expiry = toExpiryDate(expiryRaw);
    
                if (!fullName || isNaN(birth.day) || isNaN(birth.month) || isNaN(birth.year)) {
                    this.mrzError = 'تعذّر قراءة البيانات، تأكد من نسخ السطرين بشكل صحيح وكامل.';
                    return;
                }
    
                // ---- تعبئة الحقول الموجودة في النموذج ----
                const fullNameEl = document.getElementById('full_name');
                if (fullNameEl) fullNameEl.value = fullName;
    
                const dayEl = document.querySelector('[name=birth_day]');
                const monthEl = document.querySelector('[name=birth_month]');
                const yearEl = document.querySelector('[name=birth_year]');
                if (dayEl) dayEl.value = birth.day;
                if (monthEl) monthEl.value = birth.month;
                if (yearEl) yearEl.value = birth.year;
    
                const passportEl = document.getElementById('passport_number');
                if (passportEl) passportEl.value = passportNumber;
    
                // مزامنة الجنس مع Alpine (عدّل القيم 'male'/'female' لتطابق حقل الجنس الفعلي عندك)
                if (sex === 'M') this.gender = 'male';
                else if (sex === 'F') this.gender = 'female';
    
                this.mrzResult = {
                    fullName: fullName,
                    passportNumber: passportNumber,
                    nationality: nationality,
                    sex: sex === 'M' ? 'ذكر' : (sex === 'F' ? 'أنثى' : sex),
                    birth: `${birth.day}/${birth.month}/${birth.year}`,
                    expiry: `${expiry.day}/${expiry.month}/${expiry.year}`,
                };
            } catch (e) {
                this.mrzError = 'حدث خطأ أثناء تحليل الـ MRZ، تأكد من صحة النص الملصوق.';
            }
        }
    }">

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-[#d0d7de]">
                <div class="p-6 sm:p-10">

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-md flex items-start gap-3">
                            <i class="bi bi-exclamation-triangle-fill text-red-600 text-xl mt-0.5"></i>
                            <div>
                                <h4 class="font-bold text-red-800 mb-1">يرجى تصحيح الأخطاء التالية:</h4>
                                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- قسم استخراج البيانات من MRZ (اختياري تمامًا ولا يؤثر على الإرسال) --}}
                    {{-- <div class="mb-10 p-5 bg-[#f0f9ff] border border-dashed border-[#54aeff] rounded-md">
                        <div class="flex items-center gap-3 mb-3">
                            <i class="bi bi-upc-scan text-xl text-[#0969da]"></i>
                            <h3 class="text-base font-bold text-gray-900">تعبئة سريعة من MRZ (اختياري)</h3>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">
                            الصق السطرين الموجودين أسفل صفحة بيانات جواز السفر (MRZ)، وسيتم استخراج الاسم وتاريخ
                            الميلاد ورقم الجواز تلقائيًا. هذا الحقل لا يُرسَل مع النموذج وهو فقط لمساعدتك على التعبئة.
                        </p>

                        <textarea x-model="mrzInput" rows="2" dir="ltr"
                            placeholder="P<EGYSURNAME<<GIVENNAME<<<<<<<<<<<<<<<<<<<<&#10;A123456781EGY9001015M3001019<<<<<<<<<<<<<<02"
                            class="clean-input block w-full font-mono text-xs rounded-md border-[#d0d7de] bg-white text-gray-900 py-2.5 px-3 transition-shadow"></textarea>

                        <div class="flex items-center gap-3 mt-3">
                            <button type="button" @click="parseMRZ()"
                                class="inline-flex items-center gap-2 py-2 px-4 bg-[#0969da] hover:bg-[#0860ca] text-white text-sm font-semibold rounded-md transition-colors">
                                <i class="bi bi-magic"></i>
                                <span>استخراج البيانات وتعبئة الحقول</span>
                            </button>
                            <button type="button" @click="mrzInput=''; mrzResult=null; mrzError=''"
                                class="text-sm text-gray-500 hover:text-gray-700">
                                مسح
                            </button>
                        </div>

                        <p x-show="mrzError" x-text="mrzError" x-cloak class="text-xs text-red-600 mt-3"></p>

                        <div x-show="mrzResult" x-cloak
                            class="mt-3 p-3 bg-white border border-[#d0d7de] rounded-md text-xs text-gray-700 space-y-1">
                            <p><span class="font-semibold">الاسم:</span> <span x-text="mrzResult?.fullName"></span></p>
                            <p><span class="font-semibold">رقم الجواز:</span> <span
                                    x-text="mrzResult?.passportNumber"></span></p>
                            <p><span class="font-semibold">تاريخ الميلاد:</span> <span x-text="mrzResult?.birth"></span>
                            </p>
                            <p><span class="font-semibold">الجنسية (كود):</span> <span
                                    x-text="mrzResult?.nationality"></span> &mdash;
                                <span class="font-semibold">الجنس:</span> <span x-text="mrzResult?.sex"></span> &mdash;
                                <span class="font-semibold">تاريخ انتهاء الجواز:</span> <span
                                    x-text="mrzResult?.expiry"></span>
                            </p>
                            <p class="text-[11px] text-gray-400 pt-1">
                                تم تعبئة الاسم وتاريخ الميلاد ورقم الجواز تلقائيًا في الحقول أدناه. راجع الجنسية والجنس
                                يدويًا إن كانت هناك حقول لهما في نموذجك.
                            </p>
                        </div>
                    </div> --}}

                    <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-10">
                        @csrf

                        <!-- القسم الأول: البيانات الشخصية -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                                <i class="bi bi-person-badge text-xl text-[#0969da]"></i>
                                <h3 class="text-lg font-bold text-gray-900">البيانات الشخصية</h3>
                            </div>

                            <div class="flex flex-col lg:flex-row gap-6">

                                <!-- الاسم -->
                                <div class="flex-1">
                                    <label for="full_name" class="block font-semibold text-sm text-gray-700 mb-2">
                                        {{ __('الاسم بالكامل (بالإنجليزية)') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input id="full_name" name="full_name" type="text"
                                        class="clean-input block w-full rounded-md border-[#d0d7de] bg-white text-gray-900 py-2.5 px-3 text-left transition-shadow"
                                        dir="ltr" value="{{ old('full_name') }}" placeholder="الاسم بالانجليزية"
                                        maxlength="25" required />
                                    <p class="text-xs text-gray-500 mt-2">حروف إنجليزية مطابقة لجواز السفر (أقصى حد 25
                                        حرف).</p>
                                </div>

                                <!-- تاريخ الميلاد -->
                                <div class="flex-1">
                                    <label class="block font-semibold text-sm text-gray-700 mb-2">
                                        {{ __('تاريخ الميلاد') }} <span class="text-red-500">*</span>
                                    </label>

                                    <div class="flex gap-3" dir="ltr">
                                        <input name="birth_day" type="number"
                                            class="clean-input flex-1 block w-full text-center rounded-md border-[#d0d7de] bg-white py-2.5 transition-shadow"
                                            placeholder="DD" min="1" max="31"
                                            value="{{ old('birth_day') }}" required />
                                        <input name="birth_month" type="number"
                                            class="clean-input flex-1 block w-full text-center rounded-md border-[#d0d7de] bg-white py-2.5 transition-shadow"
                                            placeholder="MM" min="1" max="12"
                                            value="{{ old('birth_month') }}" required />
                                        <input name="birth_year" type="number"
                                            class="clean-input flex-1 block w-full text-center rounded-md border-[#d0d7de] bg-white py-2.5 transition-shadow"
                                            placeholder="YYYY" min="1930" max="{{ date('Y') - 18 }}"
                                            value="{{ old('birth_year') }}" required />
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 text-right">أكبر من 18 عامًا.</p>
                                </div>
                            </div>

                        </div>

                        <!-- القسم الثاني: بيانات الهوية والإقامة -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                                <i class="bi bi-globe2 text-xl text-[#0969da]"></i>
                                <h3 class="text-lg font-bold text-gray-900">بيانات الهوية والإقامة</h3>
                            </div>

                            <div class="flex flex-wrap gap-6">

                                <div class="flex-1 min-w-[220px]">
                                    <label for="blood_type" class="block font-semibold text-sm text-gray-700 mb-2">
                                        {{ __('فصيلة الدم') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select id="blood_type" name="blood_type"
                                        class="clean-input block w-full border-[#d0d7de] bg-white text-gray-900 rounded-md py-2.5 px-3 transition-shadow"
                                        required>
                                        <option value="" disabled selected>اختر...</option>
                                        @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $type)
                                            <option value="{{ $type }}"
                                                {{ old('blood_type') == $type ? 'selected' : '' }}>{{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex-1 min-w-[220px]">
                                    <label for="passport_number" class="block font-semibold text-sm text-gray-700 mb-2">
                                        {{ __('رقم جواز السفر') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input id="passport_number" name="passport_number" type="text" dir="ltr"
                                        class="clean-input block w-full text-left rounded-md py-2.5 uppercase border-[#d0d7de] bg-white text-gray-900 transition-shadow"
                                        value="{{ old('passport_number') }}" placeholder="A12345678" required />
                                </div>

                                <!-- إضافة حقل مدة الرخصة -->
                                <div class="flex-1 min-w-[220px]">
                                    <label for="license_duration"
                                        class="block font-semibold text-sm text-gray-700 mb-2">
                                        {{ __('مدة الرخصة') }} <span class="text-red-500">*</span>
                                    </label>

                                    <!-- القيمة المقروءة والمقفلة للمستخدم -->
                                    <select id="license_duration_display"
                                        class="clean-input block w-full border-[#d0d7de] bg-gray-100 text-gray-900 rounded-md py-2.5 px-3 transition-shadow opacity-75 cursor-not-allowed"
                                        disabled>
                                        <option value="1" selected>سنة واحدة</option>
                                    </select>

                                    <!-- حقل مخفي لضمان إرسال القيمة إلى السيرفر -->
                                    <input type="hidden" name="license_duration" value="1">
                                </div>

                            </div>
                        </div>

                        <!-- القسم الرابع: المرفقات والمستندات -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                                <i class="bi bi-paperclip text-xl text-[#0969da]"></i>
                                <h3 class="text-lg font-bold text-gray-900">المرفقات والمستندات المطلوبة</h3>
                            </div>

                            <div class="flex flex-col md:flex-row gap-6">

                                {{-- الصورة الشخصية / رخصة القيادة / جواز السفر --}}
                                {{-- كل صندوق بيتشغّل من الـ JavaScript اللي تحت (data-uploader) --}}
                                @foreach ($attachments as $att)
                                    <div data-uploader tabindex="0"
                                        class="flex-1 p-5 border-2 border-dashed border-[#d0d7de] rounded-md bg-[#f6f8fa] transition-all relative focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

                                        <label class="block font-semibold text-sm text-gray-700 mb-3 text-center">
                                            {{ __($att['label']) }} <span class="text-red-500">*</span>
                                        </label>

                                        <div class="text-center text-xs text-gray-400 mb-2">
                                            اضغط هنا ثم ألصق (Ctrl+V) أو اسحب الصورة
                                        </div>

                                        <input type="file" name="{{ $att['name'] }}" accept="image/*"
                                            class="block w-full text-xs text-gray-500 file:mr-0 file:py-2 file:px-4 file:w-full file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#e1e4e8] file:text-gray-900 file:cursor-pointer text-center"
                                            required>

                                        <div data-preview class="hidden mt-4">
                                            <img data-preview-img alt=""
                                                class="w-full h-32 object-cover rounded border border-gray-200">

                                            <div class="mt-2 grid grid-cols-2 gap-2">
                                                {{-- تدوير 90° مباشرة بدون نافذة --}}
                                                <button type="button" data-rotate
                                                    class="inline-flex justify-center items-center gap-2 py-2 px-3 rounded-md border border-[#d0d7de] bg-white text-xs font-semibold text-gray-800 hover:bg-[#e1e4e8] focus:outline-none focus:ring-2 focus:ring-blue-200 transition-colors disabled:opacity-50 disabled:cursor-wait">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                    <span>تدوير</span>
                                                </button>

                                                {{-- اقتصاص: بيفتح نافذة الاقتصاص --}}
                                                <button type="button" data-edit
                                                    class="inline-flex justify-center items-center gap-2 py-2 px-3 rounded-md border border-[#d0d7de] bg-white text-xs font-semibold text-gray-800 hover:bg-[#e1e4e8] focus:outline-none focus:ring-2 focus:ring-blue-200 transition-colors">
                                                    <i class="bi bi-crop"></i>
                                                    <span>اقتصاص</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                        <!-- الإقرار وزر الإرسال -->
                        <div class="flex flex-col gap-5 pt-4">
                            <div class="flex items-start gap-3 p-4 bg-[#f6f8fa] border border-[#d0d7de] rounded-md">
                                <input id="terms" type="checkbox" name="terms"
                                    class="mt-1 w-4 h-4 rounded border-gray-300 text-[#0969da] focus:ring-[#0969da] cursor-pointer"
                                    required>
                                <label for="terms" class="text-sm text-gray-700 cursor-pointer select-none">
                                    أقر بـأن الصورة الشخصية بخلفية بيضاء، وجواز السفر ساري المفعول، ورخصة القيادة
                                    المحلية صالحة، وأن البيانات دقيقة وتحت مسؤوليتي.
                                </label>
                            </div>

                            <button type="submit"
                                class="w-full sm:w-auto self-end inline-flex justify-center items-center gap-2 py-2 px-6 bg-[#2da44e] hover:bg-[#2c974b] text-white font-semibold rounded-md transition-colors border border-[#2da44e]">
                                <span> حفظ</span>
                                <i class="bi bi-credit-card"></i>
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- ========================= نافذة اقتصاص وتدوير الصورة ========================= --}}
    <div id="cropModal" dir="rtl" role="dialog" aria-modal="true" aria-label="اقتصاص الصورة"
        class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60 p-3 sm:p-6 form-shell">

        <div class="w-full max-w-3xl max-h-full flex flex-col bg-white rounded-md shadow-xl overflow-hidden">

            <!-- العنوان -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-[#d0d7de] bg-[#f6f8fa]">
                <h3 class="font-semibold text-sm text-gray-800">اقتصاص الصورة</h3>
                <button type="button" id="cropClose" aria-label="إغلاق"
                    class="text-gray-500 hover:text-gray-900 text-xl leading-none px-1">&times;</button>
            </div>

            <!-- منطقة الاقتصاص -->
            <div id="cropStage" dir="ltr" class="bg-[#1f2328] h-[50vh] sm:h-[60vh] w-full"></div>

            <!-- الأدوات -->
            <div class="px-4 py-3 border-t border-[#d0d7de] space-y-3 overflow-y-auto">

                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-gray-700">نسبة الاقتصاص:</span>

                    <button type="button" data-ratio="free"
                        class="py-1.5 px-3 rounded-md border text-xs font-semibold bg-gray-900 text-white border-gray-900">حر</button>
                    <button type="button" data-ratio="1"
                        class="py-1.5 px-3 rounded-md border text-xs font-semibold bg-white text-gray-800 border-[#d0d7de]">1:1</button>
                    <button type="button" data-ratio="0.75"
                        class="py-1.5 px-3 rounded-md border text-xs font-semibold bg-white text-gray-800 border-[#d0d7de]">3:4</button>
                    <button type="button" data-ratio="1.3333333"
                        class="py-1.5 px-3 rounded-md border text-xs font-semibold bg-white text-gray-800 border-[#d0d7de]">4:3</button>
                </div>

                <div class="flex items-center justify-between gap-2 pt-1">
                    <button type="button" id="cropReset"
                        class="py-2 px-4 rounded-md text-xs font-semibold text-gray-600 hover:bg-[#e1e4e8]">إعادة
                        ضبط</button>

                    <div class="flex gap-2">
                        <button type="button" id="cropCancel"
                            class="py-2 px-4 rounded-md border border-[#d0d7de] bg-white text-xs font-semibold hover:bg-[#e1e4e8]">إلغاء</button>
                        <button type="button" id="cropApply"
                            class="py-2 px-5 rounded-md bg-gray-900 text-white text-xs font-semibold hover:bg-gray-700">تطبيق</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            const MAX_SIZE = 2400; // أقصى بُعد للصورة الناتجة (بالبكسل)

            /* ---------------------------------------------------------------
             * 1) نافذة الاقتصاص (Cropper.js) - اقتصاص فقط
             * ------------------------------------------------------------- */
            const modal = document.getElementById('cropModal');
            const stage = document.getElementById('cropStage');
            const ratioButtons = modal.querySelectorAll('[data-ratio]');

            const ACTIVE = ['bg-gray-900', 'text-white', 'border-gray-900'];
            const INACTIVE = ['bg-white', 'text-gray-800', 'border-[#d0d7de]'];

            let cropper = null;
            let currentUploader = null;

            function markRatio(activeBtn) {
                ratioButtons.forEach(function(btn) {
                    const on = btn === activeBtn;
                    btn.classList.remove.apply(btn.classList, on ? INACTIVE : ACTIVE);
                    btn.classList.add.apply(btn.classList, on ? ACTIVE : INACTIVE);
                });
            }

            function destroyCropper() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                stage.innerHTML = '';
            }

            function openCropper(uploader) {
                if (typeof Cropper === 'undefined') {
                    alert('تعذّر تحميل مكتبة الاقتصاص، تأكد من الاتصال بالإنترنت وأعد تحميل الصفحة.');
                    return;
                }
                if (!uploader.url) return;

                currentUploader = uploader;
                markRatio(modal.querySelector('[data-ratio="free"]'));

                // نظهر المودال الأول عشان أبعاد الحاوية تتحسب صح
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');

                destroyCropper();
                const img = document.createElement('img');
                img.alt = '';
                img.className = 'block max-w-full';
                img.addEventListener('load', function() {
                    cropper = new Cropper(img, {
                        viewMode: 1,
                        dragMode: 'crop',
                        autoCropArea: 1,
                        background: false,
                        responsive: true,
                    });
                });
                stage.appendChild(img);
                img.src = uploader.url;
            }

            function closeCropper() {
                destroyCropper();
                currentUploader = null;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }

            ratioButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    if (!cropper) return;
                    const v = btn.getAttribute('data-ratio');
                    cropper.setAspectRatio(v === 'free' ? NaN : parseFloat(v));
                    markRatio(btn);
                });
            });

            document.getElementById('cropReset').addEventListener('click', function() {
                if (!cropper) return;
                cropper.reset();
                cropper.setAspectRatio(NaN);
                markRatio(modal.querySelector('[data-ratio="free"]'));
            });

            document.getElementById('cropApply').addEventListener('click', function() {
                if (!cropper || !currentUploader) return;

                const canvas = cropper.getCroppedCanvas({
                    maxWidth: MAX_SIZE,
                    maxHeight: MAX_SIZE,
                    fillColor: '#ffffff',
                    imageSmoothingQuality: 'high',
                });
                if (!canvas) return;

                const target = currentUploader;
                const newName = target.name.replace(/\.[^.]+$/, '') + '.jpg';

                canvas.toBlob(function(blob) {
                    if (!blob) return;
                    const file = new File([blob], newName, {
                        type: 'image/jpeg',
                        lastModified: Date.now(),
                    });
                    target.setFile(file);
                    closeCropper();
                }, 'image/jpeg', 0.92);
            });

            document.getElementById('cropCancel').addEventListener('click', closeCropper);
            document.getElementById('cropClose').addEventListener('click', closeCropper);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeCropper();
            });

            /* ---------------------------------------------------------------
             * 2) تدوير الصورة 90° مباشرة (بدون نافذة) عن طريق Canvas
             * ------------------------------------------------------------- */
            function rotateImage90(url, name) {
                return new Promise(function(resolve, reject) {
                    const img = new Image();

                    img.onload = function() {
                        const w = img.naturalWidth;
                        const h = img.naturalHeight;
                        const scale = Math.min(1, MAX_SIZE / Math.max(w, h));
                        const sw = Math.round(w * scale);
                        const sh = Math.round(h * scale);

                        // بعد التدوير 90° العرض والارتفاع بيتبدلوا
                        const canvas = document.createElement('canvas');
                        canvas.width = sh;
                        canvas.height = sw;

                        const ctx = canvas.getContext('2d');
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                        ctx.imageSmoothingQuality = 'high';
                        ctx.translate(canvas.width / 2, canvas.height / 2);
                        ctx.rotate(Math.PI / 2); // 90° في اتجاه عقارب الساعة
                        ctx.drawImage(img, -sw / 2, -sh / 2, sw, sh);

                        canvas.toBlob(function(blob) {
                            if (!blob) {
                                reject(new Error('toBlob failed'));
                                return;
                            }
                            resolve(new File([blob], name.replace(/\.[^.]+$/, '') + '.jpg', {
                                type: 'image/jpeg',
                                lastModified: Date.now(),
                            }));
                        }, 'image/jpeg', 0.95);
                    };

                    img.onerror = reject;
                    img.src = url;
                });
            }

            /* ---------------------------------------------------------------
             * 3) صناديق رفع الصور (سحب / لصق / اختيار + معاينة + تدوير + اقتصاص)
             * ------------------------------------------------------------- */
            document.querySelectorAll('[data-uploader]').forEach(function(box) {
                const input = box.querySelector('input[type="file"]');
                const previewWrap = box.querySelector('[data-preview]');
                const previewImg = box.querySelector('[data-preview-img]');
                const rotateBtn = box.querySelector('[data-rotate]');
                const editBtn = box.querySelector('[data-edit]');

                const uploader = {
                    url: null,
                    name: 'image.jpg',
                    setFile: setFile,
                };

                function setFile(file) {
                    if (!file || !file.type.startsWith('image/')) return;

                    const oldUrl = uploader.url;
                    uploader.url = URL.createObjectURL(file);
                    uploader.name = file.name || 'image.jpg';

                    previewImg.src = uploader.url;
                    previewWrap.classList.remove('hidden');

                    // نحط الملف في الـ input عشان يتبعت مع الفورم
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;

                    if (oldUrl) URL.revokeObjectURL(oldUrl);
                }

                input.addEventListener('change', function() {
                    setFile(input.files[0]);
                });

                box.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    box.classList.add('border-blue-500', 'bg-blue-50');
                });

                box.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    box.classList.remove('border-blue-500', 'bg-blue-50');
                });

                box.addEventListener('drop', function(e) {
                    e.preventDefault();
                    box.classList.remove('border-blue-500', 'bg-blue-50');
                    setFile(e.dataTransfer.files[0]);
                });

                box.addEventListener('paste', function(e) {
                    if (e.clipboardData && e.clipboardData.files.length) {
                        setFile(e.clipboardData.files[0]);
                    }
                });

                // تدوير مباشر: كل ضغطة = 90° يمين، من غير نافذة
                rotateBtn.addEventListener('click', function() {
                    if (!uploader.url || rotateBtn.disabled) return;
                    rotateBtn.disabled = true;

                    rotateImage90(uploader.url, uploader.name)
                        .then(setFile)
                        .catch(function() {
                            alert('تعذّر تدوير الصورة، حاول مرة أخرى.');
                        })
                        .finally(function() {
                            rotateBtn.disabled = false;
                        });
                });

                // اقتصاص: بيفتح نافذة الاقتصاص
                editBtn.addEventListener('click', function() {
                    openCropper(uploader);
                });
            });
        })();
    </script>
</x-app-layout>
