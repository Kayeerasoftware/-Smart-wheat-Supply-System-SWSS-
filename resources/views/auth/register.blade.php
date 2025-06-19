<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("{{ asset('images/1.jpg') }}") center/cover no-repeat;
            z-index: -2;
            opacity: 0.9;
        }
        
        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        
        .gradient-text {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            position: relative;
            overflow: hidden;
            color: #fff;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        
        input, select, textarea {
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        
        input:focus, select:focus, textarea:focus {
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.3);
            background: rgba(255, 255, 255, 0.2);
        }
        
        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .curved-logo {
            border-radius: 12px;
            width: 5rem;
            height: 5rem;
            object-fit: contain;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
        }
        
        .curved-logo:hover {
            transform: scale(1.05);
        }
        
        /* Custom scrollbar */
        .glass-card::-webkit-scrollbar {
            width: 6px;
        }
        
        .glass-card::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        .glass-card::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }
        
        .glass-card::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }
        
        /* File upload styling */
        .file-upload {
            position: relative;
            display: inline-block;
            cursor: pointer;
            width: 100%;
        }
        
        .file-upload input[type=file] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            border: 2px dashed rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .file-upload-label:hover {
            border-color: rgba(79, 172, 254, 0.5);
            background: rgba(255, 255, 255, 0.1);
        }
        
        .file-upload input[type=file]:focus + .file-upload-label {
            border-color: rgba(79, 172, 254, 0.8);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.3);
        }
        
        .supplier-fields {
            display: none;
            animation: slideDown 0.3s ease-out;
        }
        
        .supplier-fields.show {
            display: block;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .file-preview {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            font-size: 0.875rem;
            color: #e5e7eb;
        }
        
        @media (max-width: 768px) {
            .curved-logo {
                width: 4rem;
                height: 4rem;
                border-radius: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="glass-card p-8 mx-4">
        <div class="text-center mb-6">
            <div class="logo-container">
                <img src="{{ asset('images/2.jpeg') }}" alt="SWSS Logo" class="curved-logo">
            </div>
            <h2 class="text-3xl font-space gradient-text font-bold">WHEAT SCM REGISTER</h2>
            <p class="text-gray-300 mt-2">Join our smart wheat supply network</p>
        </div>
        
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-600/80 text-white rounded-lg">{{ session('error') }}</div>
        @endif
        
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-600/80 text-white rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label for="username" class="block text-gray-200 mb-1 font-medium">Username</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('username') border-red-400 @enderror" value="{{ old('username') }}" required>
                @error('username')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-gray-200 mb-1 font-medium">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('email') border-red-400 @enderror" value="{{ old('email') }}" required>
                @error('email')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-200 mb-1 font-medium">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('password') border-red-400 @enderror" required>
                @error('password')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-200 mb-1 font-medium">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400" required>
            </div>
            
            <div class="mb-4">
                <label for="role" class="block text-gray-200 mb-1 font-medium">My Responsibility</label>
                <select id="role" name="role" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 outline-none focus:border-blue-400 @error('role') border-red-400 @enderror" required>
<<<<<<< HEAD
                    <option value="">Select Responsibility</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
=======
                    <option value="">Select Role</option>
>>>>>>> 025298ec537c40e2593fd2784eae476136c98df3
                    <option value="farmer" {{ old('role') == 'farmer' ? 'selected' : '' }}>Farmer</option>
                    <option value="supplier" {{ old('role') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    <option value="manufacturer" {{ old('role') == 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                    <option value="distributor" {{ old('role') == 'distributor' ? 'selected' : '' }}>Distributor</option>
                    <option value="retailer" {{ old('role') == 'retailer' ? 'selected' : '' }}>Retailer</option>
                </select>
                @error('role')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <!-- Supplier-specific fields -->
            <div id="supplier-fields" class="supplier-fields {{ old('role') == 'supplier' ? 'show' : '' }}">
                <div class="mb-4 p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                    <h3 class="text-blue-300 font-semibold mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Supplier Application Requirements
                    </h3>
                    <p class="text-gray-300 text-sm mb-4">As a supplier, please provide additional documentation to complete your registration.</p>
                    
                    <div class="mb-4">
                        <label for="business_name" class="block text-gray-200 mb-1 font-medium">Business Name</label>
                        <input type="text" id="business_name" name="business_name" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('business_name') border-red-400 @enderror" value="{{ old('business_name') }}" placeholder="Enter your business name">
                        @error('business_name')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="business_description" class="block text-gray-200 mb-1 font-medium">Business Description</label>
                        <textarea id="business_description" name="business_description" rows="3" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('business_description') border-red-400 @enderror" placeholder="Describe your business operations and experience">{{ old('business_description') }}</textarea>
                        @error('business_description')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-200 mb-1 font-medium">Application Documents</label>
                        <div class="space-y-3">
                            <div class="file-upload">
                                <input type="file" id="application_pdf" name="application_pdf" accept=".pdf" onchange="previewFile(this, 'pdf-preview')">
                                <label for="application_pdf" class="file-upload-label">
                                    <svg class="w-6 h-6 mr-2 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-gray-200">Upload Application PDF</span>
                                </label>
                                <div id="pdf-preview" class="file-preview" style="display: none;"></div>
                            </div>
                            
                            <div class="file-upload">
                                <input type="file" id="business_image" name="business_image" accept="image/*" onchange="previewFile(this, 'image-preview')">
                                <label for="business_image" class="file-upload-label">
                                    <svg class="w-6 h-6 mr-2 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-gray-200">Upload Business Image</span>
                                </label>
                                <div id="image-preview" class="file-preview" style="display: none;"></div>
                            </div>
                        </div>
                        @error('application_pdf')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
                        @error('business_image')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="phone" class="block text-gray-200 mb-1 font-medium">Phone</label>
                <input type="tel" id="phone" name="phone" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('phone') border-red-400 @enderror" value="{{ old('phone') }}">
                @error('phone')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-6">
                <label for="address" class="block text-gray-200 mb-1 font-medium">Address</label>
                <textarea id="address" name="address" rows="3" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('address') border-red-400 @enderror">{{ old('address') }}</textarea>
                @error('address')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <button type="submit" class="btn-primary w-full py-3 rounded-xl font-semibold text-lg mb-4 hover:shadow-lg transition-all">
                REGISTER
            </button>
            
            <div class="text-center text-gray-300">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-blue-300 font-medium hover:underline">Login here</a>
            </div>
        </form>
    </div>

    <script>
        // Role selection handler
        document.getElementById('role').addEventListener('change', function() {
            const supplierFields = document.getElementById('supplier-fields');
            const selectedRole = this.value;
            
            if (selectedRole === 'supplier') {
                supplierFields.classList.add('show');
            } else {
                supplierFields.classList.remove('show');
                // Clear supplier-specific fields when role changes
                clearSupplierFields();
            }
        });
        
        // File preview handler
        function previewFile(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
                const fileType = file.type;
                
                if (fileType.startsWith('image/')) {
                    // For images, show thumbnail
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <div class="flex items-center space-x-2">
                                <img src="${e.target.result}" alt="Preview" class="w-8 h-8 rounded object-cover">
                                <span>${file.name} (${fileSize} MB)</span>
                            </div>
                        `;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    // For PDFs, show file info
                    preview.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <svg class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            <span>${file.name} (${fileSize} MB)</span>
                        </div>
                    `;
                    preview.style.display = 'block';
                }
            } else {
                preview.style.display = 'none';
            }
        }
        
        // Clear supplier fields when role changes
        function clearSupplierFields() {
            const fields = ['business_name', 'business_type', 'business_description'];
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    if (field.type === 'select-one') {
                        field.selectedIndex = 0;
                    } else {
                        field.value = '';
                    }
                }
            });
            
            // Clear file inputs
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.value = '';
            });
            
            // Hide previews
            const previews = document.querySelectorAll('.file-preview');
            previews.forEach(preview => {
                preview.style.display = 'none';
            });
        }
        
        // Initialize form state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            if (roleSelect.value === 'supplier') {
                document.getElementById('supplier-fields').classList.add('show');
            }
        });
    </script>
</body>
</html>