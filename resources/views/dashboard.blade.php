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
    @endphp

    <div class="py-10 bg-gray-50 min-h-screen form-shell" dir="rtl">
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

                    <div class="overflow-x-auto -mx-6 sm:mx-0">
                        <table class="w-full text-sm text-right text-gray-600 border-collapse">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                                <tr>
                                    <th scope="col" class="px-6 py-3.5 font-bold rounded-r-xl">#</th>
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
                                                <button @click="toggleStatus()" :disabled="isLoading"
                                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl font-bold text-xs text-white shadow-md transition-all duration-200"
                                                    :class="status === 'completed' ?
                                                        'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20' :
                                                        'bg-gray-600 hover:bg-gray-700 shadow-gray-600/20'">

                                                    <i class="bi"
                                                        :class="isLoading ? 'bi-arrow-repeat animate-spin' : (
                                                            status === 'completed' ? 'bi-check-all' : 'bi-check-lg')"></i>

                                                    <span x-text="status === 'completed' ? 'تم (مكتمل)' : 'تم'"></span>
                                                </button>
                                            @else
                                                <template x-if="status === 'pending'">
                                                    <div class="flex justify-center items-center gap-2">
                                                        <a href="{{ route('customers.show', $customer) }}"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors">
                                                            <i class="bi bi-eye"></i>
                                                            <span>عرض</span>
                                                        </a>
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
                                                        تم الطلب
                                                    </span>
                                                </template>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-16 text-center">
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
    </div>
</x-app-layout>
