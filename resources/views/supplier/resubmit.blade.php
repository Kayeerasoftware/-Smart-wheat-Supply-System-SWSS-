<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SWSS - Resubmit Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Resubmit Application</h1>
                <p class="text-gray-300">Please upload corrected documents</p>
            </div>

            <div class="glass-card p-8">
                <form action="{{ route('supplier.resubmit.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Application PDF</label>
                        <input type="file" name="application_pdf" accept=".pdf" required
                               class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600">
                        <p class="text-gray-400 text-sm mt-1">Upload a corrected PDF with all required information</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Business Image (Optional)</label>
                        <input type="file" name="business_image" accept="image/*"
                               class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600">
                        <p class="text-gray-400 text-sm mt-1">Upload an updated business image if needed</p>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" 
                                class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-upload mr-2"></i>
                            Submit Application
                        </button>
                        <a href="{{ route('supplier.validation-failed') }}" 
                           class="flex-1 bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition-colors text-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 