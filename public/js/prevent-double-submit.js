document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            // Jika form sudah disubmit, cegah pengiriman ulang ganda
            if (this.dataset.submitted) {
                e.preventDefault();
                return;
            }

            // Validasi form HTML5 dasar
            if (!this.checkValidity()) {
                return; 
            }

            // Tandai form sebagai sudah disubmit
            this.dataset.submitted = true;

            // Dapatkan tombol submit
            const submitBtn = this.querySelector('button[type="submit"]') || this.querySelector('input[type="submit"]');
            
            if (submitBtn) {
                const originalWidth = submitBtn.offsetWidth;

                // Nonaktifkan tombol untuk prevent double click
                submitBtn.disabled = true;
                
                // Tambahkan class agar terlihat seperti memproses
                submitBtn.classList.add('opacity-70', 'cursor-not-allowed', 'pointer-events-none');
                
                // Jaga ukuran lebar agar layout tidak bergeser/patah saat text diubah
                if (originalWidth > 0 && !submitBtn.style.width) {
                    submitBtn.style.minWidth = originalWidth + 'px';
                }

                // Ubah konten tombol menjadi loading state spinner berputar
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                `;
            }
        });
    });
});
