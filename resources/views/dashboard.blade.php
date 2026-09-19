<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4" dir="rtl">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight">
                    {{ __('لوحة التحكم') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">إدارة ومتابعة قائمة العملاء وطلبات رخصة القيادة الدولية.</p>
            </div>

            <a href="{{ route('customers.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-xl font-bold text-sm text-white shadow-lg shadow-indigo-600/30 transition-all duration-200">
                <i class="bi bi-plus-lg text-base"></i>
                <span>إضافة عميل جديد</span>
            </a>
        </div>
    </x-slot>

    <!-- CDN لأيقونات Bootstrap إذا لم تكن مدمجة بالمشروع -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- مكتبة تصدير الإكسيل -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        .form-shell {
            font-family: 'Cairo', ui-sans-serif, system-ui, sans-serif;
        }
    </style>

    @php
        // ألوان مميزة لكل فئة ترخيص
        $licenseColors = [
            'A' => 'bg-gray-100 text-gray-700 ring-1 ring-gray-200',
            'B' => 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200',
            'C' => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',
            'D' => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200',
        ];

        // بيانات العملاء (الاسم ورقم الجواز فقط) لاستخدامها في التصدير
        $customersForExport = (method_exists($customers, 'getCollection')
            ? $customers->getCollection()
            : $customers
        )->map(fn($c) => $c->toArray());
    @endphp

    <div class="py-10 bg-gray-50 min-h-screen form-shell" dir="rtl" x-data="{
        selectedCustomers: [],
        customersData: {{ Illuminate\Support\Js::from($customersForExport) }},
        toggleSelectAll(checked) {
            this.selectedCustomers = checked ? this.customersData.map(c => String(c.id)) : [];
        },
        get allSelected() {
            return this.customersData.length > 0 && this.selectedCustomers.length === this.customersData.length;
        },
        exportSelected() {
            if (this.selectedCustomers.length === 0) {
                alert('يرجى تحديد عميل واحد على الأقل قبل التصدير');
                return;
            }
    
            const rows = this.customersData
                .filter(c => this.selectedCustomers.includes(String(c.id)))
                .map(c => ({ 'الاسم بالكامل': c.full_name, 'رقم الجواز': c.passport_number }));
    
            const worksheet = XLSX.utils.json_to_sheet(rows);
            worksheet['!cols'] = [{ wch: 30 }, { wch: 20 }];
    
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'العملاء');
            XLSX.writeFile(workbook, 'customers_export.xlsx');
        },
        async sendToApi() {
            if (this.selectedCustomers.length === 0) {
                alert('يرجى تحديد عميل واحد على الأقل أولاً');
                return;
            }
    
            // تفكيك الـ Proxy واستخراج بيانات العملاء المحددات
            const cleanData = JSON.parse(JSON.stringify(
                this.customersData.filter(c => this.selectedCustomers.includes(String(c.id)))
            ));
    
            const baseUrl = window.location.origin + '/storage/';
    
            for (const customer of cleanData) {
                // تحويل التاريخ لـ DD/MM/YYYY
                let formattedDob = '';
                if (customer.birth_date) {
                    const parts = customer.birth_date.split('-');
                    if (parts.length === 3) {
                        formattedDob = `${parts[2]}/${parts[1]}/${parts[0]}`;
                    }
                }
    
                // تجهيز رابط الصور مع التأكد من إضافة Domin / Storage
                const buildUrl = (path) => {
                    if (!path) return null;
                    return path.startsWith('http') ? path : baseUrl + path;
                };
    
                const payload = {
                    name: customer.full_name,
                    dateOfBirth: formattedDob,
                    passportID: customer.passport_number,
                    bloodGroup: customer.blood_type,
                    gender: 'male',
                    phoneNumber: customer.phone_number || '',
                    personalImgUrl: buildUrl(customer.personal_photo),
                    passportImgUrl: buildUrl(customer.passport_photo),
                    licenseImgUrl: buildUrl(customer.local_license)
                };
    
                try {
                    const response = await fetch('http://localhost:3000/api/issue-license', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
    
                    const result = await response.json();
                    console.log(`تم إرسال العميل ${customer.full_name}:`, result);
                } catch (error) {
                    console.error(`خطأ أثناء إرسال العميل ${customer.full_name}:`, error);
                }
            }
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- رسالة النجاح عند الإضافة أو التعديل --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-2 font-bold text-emerald-700"
                    role="alert">
                    <i class="bi bi-check-circle-fill text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl shadow-gray-200/60 rounded-3xl border border-gray-100">
                <div class="p-6 sm:p-8 text-gray-900">

                    {{-- شريط أدوات التحديد والتصدير --}}
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-semibold text-gray-500">
                            <span x-show="selectedCustomers.length > 0"
                                x-text="`تم تحديد ${selectedCustomers.length} عميل`"></span>
                        </span>

                        <div class="flex items-center gap-2">
                            @if (auth()->user()->email == 'eslam@gmail.com')
                                <button @click="sendToApi()" :disabled="selectedCustomers.length === 0"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-sm text-black shadow-md transition-all duration-200"
                                    :class="selectedCustomers.length === 0 ?
                                        'bg-gray-300 cursor-not-allowed' :
                                        'bg-slate-700 hover:bg-slate-800 shadow-slate-700/20'">
                                    <i class="bi bi-terminal"></i>
                                    <span>طباعة</span>
                                </button>
                            @endif
                            <button @click="exportSelected()" :disabled="selectedCustomers.length === 0"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-sm text-black shadow-md transition-all duration-200"
                                :class="selectedCustomers.length === 0 ?
                                    'bg-gray-300 cursor-not-allowed' :
                                    'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20'">
                                <i class="bi bi-file-earmark-excel"></i>
                                <span>تصدير Excel للمحددين</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto -mx-6 sm:mx-0">
                        <table class="w-full text-sm text-right text-gray-600 border-collapse">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                                <tr>
                                    <th scope="col" class="px-6 py-3.5 font-bold rounded-r-xl">
                                        <input type="checkbox" :checked="allSelected"
                                            @change="toggleSelectAll($event.target.checked)"
                                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </th>
                                    <th scope="col" class="px-6 py-3.5 font-bold">#</th>
                                    <th scope="col" class="px-6 py-3.5 font-bold">الاسم بالكامل</th>
                                    <th scope="col" class="px-6 py-3.5 font-bold">رقم الجواز</th>
                                    <th scope="col" class="px-6 py-3.5 font-bold text-center rounded-l-xl">الإجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($customers as $customer)
                                    <tr class="hover:bg-indigo-50/30 transition-colors" x-data="{
                                        status: '{{ $customer->status }}',
                                        isLoading: false,
                                        toggleStatus() {
                                            this.isLoading = true;
                                            axios.patch('{{ route('customers.toggleStatus', $customer) }}')
                                                .then(response => {
                                                    if (response.data.success) {
                                                        this.status = response.data.status;
                                                    }
                                                })
                                                .catch(error => {
                                                    alert('حدث خطأ أثناء التحديث');
                                                })
                                                .finally(() => {
                                                    this.isLoading = false;
                                                });
                                        }
                                    }">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" value="{{ $customer->id }}"
                                                x-model="selectedCustomers"
                                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-400 whitespace-nowrap">
                                            {{ $customer->id }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm shrink-0">
                                                    {{ mb_substr($customer->full_name, 0, 1) }}
                                                </div>
                                                <span
                                                    class="font-semibold text-gray-900">{{ $customer->full_name }}</span>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-gray-600 tracking-wide" dir="ltr">
                                            {{ $customer->passport_number }}
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            @if (auth()->user()->email == 'eslam@gmail.com')
                                                <div
                                                    class="flex flex-wrap items-center gap-3 p-3 bg-white rounded-2xl border border-gray-100 shadow-sm">

                                                    <!-- قسم نموذج إدخال رقم الرخصة -->
                                                    <div x-data="licenseManager({ customerId: {{ $customer->id }}, currentLicense: '{{ $customer->license_number }}' })"
                                                        class="flex items-center gap-1.5 flex-1 min-w-[260px]">

                                                        <div class="relative w-full">
                                                            <input type="text" x-model="licenseNumber"
                                                                placeholder="أدخل رقم الرخصة" :disabled="loading"
                                                                :readonly="isSaved"
                                                                :class="isSaved ?
                                                                    'bg-gray-100 text-gray-500 cursor-not-allowed border-gray-200' :
                                                                    'bg-gray-50 text-gray-800 border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20'"
                                                                class="w-full pl-3 pr-3 py-1.5 text-xs font-medium rounded-xl transition-all duration-200 focus:outline-none disabled:bg-gray-100 placeholder:text-gray-400" />
                                                        </div>

                                                        <!-- إخفاء الزر تماماً إذا تم الحفظ -->
                                                        <template x-if="!isSaved">
                                                            <button @click="saveLicense" :disabled="loading"
                                                                class="px-3.5 py-1.5 text-xs font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 active:scale-95 disabled:opacity-50 disabled:pointer-events-none transition-all duration-200 flex items-center justify-center min-w-[65px] shadow-sm shadow-blue-600/20">
                                                                <span x-show="!loading">حفظ</span>
                                                                <span x-show="loading" class="animate-spin text-xs">
                                                                    <i class="bi bi-arrow-repeat"></i>
                                                                </span>
                                                            </button>
                                                        </template>
                                                    </div>

                                                    <!-- أزرار الإجراءات والروابط -->
                                                    <div class="flex items-center gap-2">

                                                        <!-- زر عرض -->
                                                        <a href="{{ route('customers.show', $customer) }}"
                                                            class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 rounded-xl transition-all duration-200">
                                                            عرض
                                                        </a>

                                                        <!-- زر الكارت -->
                                                        <a href="{{ route('customers.card', $customer) }}"
                                                            target="_blank"
                                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl text-xs font-semibold transition-all duration-200 border border-blue-200/60">
                                                            <i class="bi bi-card-heading text-sm"></i>
                                                            <span>الكارت</span>
                                                        </a>

                                                        <!-- الحالة والتغيير المباشر -->
                                                        <template x-if="status === 'admin' || status === 'completed'">
                                                            <button @click="toggleStatus()" :disabled="isLoading"
                                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl font-bold text-xs text-white shadow-sm transition-all duration-200 active:scale-95 disabled:opacity-50"
                                                                :class="status === 'completed' ?
                                                                    'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20' :
                                                                    'bg-slate-700 hover:bg-slate-800 shadow-slate-700/20'">

                                                                <i class="bi"
                                                                    :class="isLoading ? 'bi-arrow-repeat animate-spin' : (
                                                                        status === 'completed' ?
                                                                        'bi-check-all text-sm' : 'bi-check-lg')"></i>

                                                                <span
                                                                    x-text="status === 'completed' ? 'تم الطباعة' : 'اكتملت'"></span>
                                                            </button>
                                                        </template>

                                                        <!-- حالة الانتظار -->
                                                        <template x-if="status !== 'admin' && status !== 'completed'">
                                                            <span
                                                                class="font-semibold text-amber-700 bg-amber-50 border border-amber-200/60 px-3 py-1.5 rounded-xl text-xs inline-flex items-center gap-1.5">
                                                                <i class="bi bi-clock-history animate-pulse"></i>
                                                                <span>في انتظار الإرسال للأدمن</span>
                                                            </span>
                                                        </template>

                                                    </div>
                                                </div>
                                            @else
                                                <template x-if="status === 'pending'">
                                                    <div class="flex justify-center items-center gap-2">
                                                        <!-- زر إرسال إلى الأدمن -->
                                                        <button
                                                            @click="
                isLoading = true;
                axios.patch('{{ route('customers.toAdmin', $customer) }}')
                    .then(response => {
                        if (response.data.success) {
                            status = response.data.status || 'sent_to_admin';
                        }
                    })
                    .catch(error => {
                        alert('حدث خطأ أثناء الإرسال');
                    })
                    .finally(() => {
                        isLoading = false;
                    });
            "
                                                            :disabled="isLoading"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors">
                                                            <i class="bi"
                                                                :class="isLoading ? 'bi-arrow-repeat animate-spin' :
                                                                    'bi-send'"></i>
                                                            <span>إرسال للطباعة</span>
                                                        </button>

                                                        <!-- زر عرض -->
                                                        <a href="{{ route('customers.show', $customer) }}"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors">
                                                            <i class="bi bi-eye"></i>
                                                            <span>عرض</span>
                                                        </a>

                                                        <!-- زر تعديل -->
                                                        <a href="{{ route('customers.edit', $customer) }}"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 transition-colors">
                                                            <i class="bi bi-pencil"></i>
                                                            <span>تعديل</span>
                                                        </a>
                                                    </div>
                                                </template>

                                                <template x-if="status !== 'pending'">
                                                    <span
                                                        class="font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg text-xs">
                                                        تم الإرسال للطباعة
                                                    </span>
                                                </template>
                                                <template x-if="status === 'completed'">
                                                    <span
                                                        class="font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg text-xs">
                                                        <i class="bi bi-printer me-1"></i> تم الطباعة
                                                    </span>
                                                </template>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                                <div
                                                    class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center text-2xl">
                                                    <i class="bi bi-people"></i>
                                                </div>
                                                <span class="font-semibold text-gray-500">لا يوجد عملاء مسجلين
                                                    حالياً</span>
                                                <a href="{{ route('customers.create') }}"
                                                    class="text-indigo-600 hover:text-indigo-700 font-bold text-sm">
                                                    + إضافة أول عميل
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- روابط التنقل للصفحات (Pagination) --}}
                    @if (method_exists($customers, 'links'))
                        <div class="mt-6 px-1">
                            {{ $customers->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('licenseManager', (config) => ({
                    licenseNumber: config.currentLicense || '',
                    isSaved: Boolean(config.currentLicense),
                    loading: false,

                    async saveLicense() {
                        if (!this.licenseNumber.trim()) {
                            alert('يرجى إدخال رقم الرخصة أولاً');
                            return;
                        }

                        this.loading = true;

                        try {
                            const response = await axios.patch(
                                `/customers/${config.customerId}/license`, {
                                    license_number: this.licenseNumber
                                });

                            this.isSaved = true; // تحويل الحقل إلى قراءة فقط فور الحفظ
                            alert(response.data.message || 'تم حفظ رقم الرخصة بنجاح');
                        } catch (error) {
                            alert(error.response?.data?.message || 'حدث خطأ أثناء الحفظ');
                        } finally {
                            this.loading = false;
                        }
                    }
                }));
            });
        </script>
    </div>
</x-app-layout>
