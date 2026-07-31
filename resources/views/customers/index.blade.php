<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <title>قائمة العملاء المسجلين</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body class="bg-light p-4">

  <div class="container bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>قائمة العملاء المسجلين</h2>
      <a href="{{ route('customers.create') }}" class="btn btn-primary">+ إضافة عميل جديد</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered text-center align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>الاسم بالكامل</th>
          <th>الجنسية</th>
          <th>رقم الجواز</th>
          <th>فئة الترخيص</th>
          <th>تاريخ التسجيل</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($customers as $customer)
        <tr>
          <td>{{ $customer->id }}</td>
          <td>{{ $customer->full_name }}</td>
          <td>{{ $customer->nationality }}</td>
          <td>{{ $customer->passport_number }}</td>
          <td><span class="badge bg-info text-dark">{{ $customer->license_category }}</span></td>
          <td>{{ $customer->created_at->format('Y-m-d') }}</td>
          <td>
            <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-info me-1"><i class="bi bi-eye"></i> عرض</a>
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i> تعديل</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-muted">لا يوجد عملاء مسجلين حتى الآن.</td>
        </tr>
        @endforelse
      </tbody>
    </table>

    @if(method_exists($customers, 'links'))
    <div class="mt-3">
      {{ $customers->links() }}
    </div>
    @endif
  </div>

</body>

</html>