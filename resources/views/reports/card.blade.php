<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Data Overlay</title>
    <style>
        /* إعدادات أبعاد كارت PVC القياسي (CR80) للطباعة الأفقية */
        @page {
            size: 85.6mm 53.98mm landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: transparent !important;
            /* خلفية شفافة تماماً */
            background-color: transparent !important;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* الحاوية الأساسية بمقاس الكارت تماماً مع وضع صورة الكارت كخلفية */
        .card-overlay {
            width: 85.6mm;
            height: 53.98mm;
            position: relative;
            /* استخدام الصورة المرفقة كخلفية */
            /* background-image: url('./preveiw.jpeg'); */
            /* background-size: cover; */
            /* background-position: center; */
            /* background-repeat: no-repeat; */
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            border-radius: 7px;
        }

        /* 1. رقم الرخصة (أعلى اليمين) */
        .license-number {
            position: absolute;
            top: 5mm;
            right: 7mm;
            font-size: 11px;
            font-weight: bold;
            color: #000;
            letter-spacing: 0.5px;
        }

        /* 2. صورة الشخص (أعلى اليسار داخل المربع المخصص) */
        .photo-box {
            position: absolute;
            top: 14.5mm;
            left: 3.3mm;
            width: 19.5mm;
            height: 26.5mm;
            border-radius: 6px;
            overflow: hidden;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* 3. تواريخ الإصدار والانتهاء (تحت الصورة جهة اليسار) */
        .dates-box {
            position: absolute;
            top: 42.5mm;
            left: 10.5mm;
            display: flex;
            flex-direction: column;
            gap: 2.4mm;
            font-size: 8.5px;
            font-weight: bold;
            color: #000;
        }

        /* 4. قائمة القيم والبيانات */
        .data-values {
            position: absolute;
            top: 13.2mm;
            left: 41.5mm;
            display: flex;
            flex-direction: column;
            gap: 1.41mm;
            font-size: 8px;
            font-weight: bold;
            color: #000;
            /* text-transform: uppercase; */
        }

        .data-row {
            height: 2.8mm;
            display: flex;
            align-items: center;
        }

        /* 5. رمز QR Code (منتصف/يمين الكارت) */
        .qr-box {
            position: absolute;
            top: 17.5mm;
            right: 2mm;
            width: 17.5mm;
            height: 17.5mm;

        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* إخفاء الخلفية والظل تماماً عند الطباعة الفعلية حتى لا تُطبع على الكارت الجاهز */
        @media print {

            html,
            body {
                background: none !important;
                background-color: transparent !important;
                -webkit-print-color-adjust: exact;
            }

            .card-overlay {
                background-image: none !important;
                /* إخفاء الصورة المرجعية */
                background-color: transparent !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="card-overlay">
        <!-- رقم الرخصة -->

        <!-- الصورة الشخصية -->
        <div class="photo-box">
            <img src="{{ asset('storage/' . $customer->personal_photo) }}" alt="Photo">
        </div>

        <!-- تواريخ ISS / EXP -->
        <div class="dates-box">
            <div>{{ $customer->created_at->format('d/m/Y') }}</div>
            <div>{{ $customer->created_at->copy()->addYear()->subDay()->format('d/m/Y') }}</div>
        </div>
        <!-- البيانات الديناميكية فقط -->
        <div class="data-values">
            <div class="data-row">{{ $customer->full_name }}</div>
            <div class="data-row">{{ \Carbon\Carbon::parse($customer->birth_date)->format('d/m/Y') }}</div>
            <div class="data-row">Sudan</div>
            <div class="data-row">KSA</div>
            <div class="data-row">{{ $customer->passport_number }}</div>
            <div class="data-row">Male</div>
            <div class="data-row">{{ $customer->blood_type }}</div>
            <div class="data-row">Sudan License</div>
            <div class="data-row">D</div>
        </div>


        <!-- QR Code -->
        <div class="qr-box">
            <!-- استدعاء مكتبة QR Code خفيفة تدعم الخلفية الشفافة -->
            <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>

            <!-- عنصر الصورة المحدد عليه النص عبر data-value -->
            <img id="qrImage" data-value="{{ url('/verify/' . $customer->customer_code) }}" alt="QR Code">

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const imgElement = document.getElementById("qrImage");
                    const textToGenerate = imgElement.getAttribute("data-value");

                    if (textToGenerate) {
                        const typeNumber = 0; // تحديد الحجم تلقائياً
                        const errorCorrectionLevel = 'H'; // مستوى تصحيح الأخطاء High

                        const qr = qrcode(typeNumber, errorCorrectionLevel);
                        qr.addData(textToGenerate);
                        qr.make();

                        // إنشاء SVG كـ Data URL بخلفية شفافة وحفظه داخل src الصورة
                        const cellSize = 5;
                        const margin = 0;
                        const svgData = qr.createSvgTag(cellSize, margin);

                        // جعل خلفية الـ SVG شفافة ولون الـ QR أسود
                        const transparentSvg = svgData
                            .replace(/fill="white"/g, 'fill="none"')
                            .replace(/fill="black"/g, 'fill="#000000"');

                        imgElement.src = "data:image/svg+xml;charset=utf-8," + encodeURIComponent(transparentSvg);
                    }
                });
            </script>
        </div>
    </div>

</body>

</html>
