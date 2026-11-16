<div id="toast" class="fixed bottom-6 right-6 z-50 transform translate-y-24 opacity-0 transition-all duration-300 bg-navy-900 border border-blue-500/40 px-5 py-3 rounded-xl shadow-2xl flex items-center space-x-3 text-xs text-gray-200">
    <i class="fa-solid fa-circle-info text-blue-400 text-base"></i>
    <span id="toast-message">Notifikasi sistem</span>
</div>

<script>
    function showNotification(message) {
        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toast-message');
        toastMsg.innerText = message;
        toast.classList.remove('translate-y-24', 'opacity-0');
        setTimeout(() => toast.classList.add('translate-y-24', 'opacity-0'), 3500);
    }

    @if (session('toast'))
        window.addEventListener('DOMContentLoaded', () => showNotification(@json(session('toast'))));
    @endif

    @if ($errors->any())
        window.addEventListener('DOMContentLoaded', () => showNotification(@json($errors->first())));
    @endif
</script>
