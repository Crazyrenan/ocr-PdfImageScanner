@if (session('status'))
    <style>
        .modal-backdrop {
            position: fixed; inset: 0; background-color: rgba(17, 24, 39, 0.8);
            z-index: 50; animation: fadeIn 0.3s ease-out;
            display: flex; align-items: center; justify-content: center;
        }
        .modal-content {
            background-color: #1f2937; color: white; padding: 2.5rem; border-radius: 0.75rem;
            width: 100%; max-width: 28rem; text-align: center; border: 1px solid #374151;
            animation: scaleUp 0.3s ease-out;
        }
        .modal-icon { width: 80px; height: 80px; margin-left: auto; margin-right: auto; margin-bottom: 1.5rem; }
        .checkmark__circle { stroke-dasharray: 166; stroke-dashoffset: 166; stroke-width: 3; stroke-miterlimit: 10; stroke: #10B981; fill: none; animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards; }
        .checkmark { width: 80px; height: 80px; border-radius: 50%; display: block; stroke-width: 3; stroke: #fff; stroke-miterlimit: 10; margin: auto; box-shadow: inset 0px 0px 0px #10B981; animation: fill-success .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both; }
        .checkmark__check { transform-origin: 50% 50%; stroke-dasharray: 48; stroke-dashoffset: 48; animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards; }
        .cross__circle { stroke-dasharray: 166; stroke-dashoffset: 166; stroke-width: 3; stroke-miterlimit: 10; stroke: #EF4444; fill: none; animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards; }
        .cross { width: 80px; height: 80px; border-radius: 50%; display: block; stroke-width: 3; stroke: #fff; stroke-miterlimit: 10; margin: auto; }
        .cross__path { transform: translate(16px, 16px) scale(0.6); stroke-dasharray: 48; stroke-dashoffset: 48; animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards; }
        @keyframes stroke { 100% { stroke-dashoffset: 0; } }
        @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
        @keyframes fill-success { 100% { box-shadow: inset 0px 0px 0px 40px #10B981; } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleUp { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    </style>

    <div id="statusModal" class="modal-backdrop">
        <div class="modal-content">
            <div class="modal-icon">
                @if (session('status') == 'success')
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                @else
                    <svg class="cross" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="cross__circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="cross__path" fill="none" d="M16 16 36 36 M36 16 16 36" />
                    </svg>
                @endif
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">{{ session('status') == 'success' ? 'Success!' : 'Error!' }}</h2>
                <p class="text-gray-400 mb-6">{{ session('message') }}</p>
                <button id="closeModalButton" class="w-full px-4 py-2 rounded-md {{ session('status') == 'success' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-red-600 hover:bg-red-700' }} text-white font-semibold transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('statusModal');
        const closeModalButton = document.getElementById('closeModalButton');

        if (modal && closeModalButton) {
            closeModalButton.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }
        @if (session('status') == 'success' && session('document_id'))
            setTimeout(function () {
                const isPdf = {{ session('is_pdf') ? 'true' : 'false' }};
                const documentId = {{ session('document_id') }};
                let redirectUrl = '';

                if (isPdf) {
                    redirectUrl = `{{ url('/documents') }}/${documentId}`;
                } else {
                    redirectUrl = `{{ url('/images') }}/${documentId}`;
                }
                
                window.location.href = redirectUrl;

            }, 2000); // Wait 2 seconds before redirecting
        @endif
    </script>
@endif