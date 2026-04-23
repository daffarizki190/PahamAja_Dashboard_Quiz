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

                // Pastikan ada class animasi spin di halaman
                if (!document.getElementById('pahamaja-spinner-style')) {
                    const style = document.createElement('style');
                    style.id = 'pahamaja-spinner-style';
                    style.innerHTML = `
                        @keyframes pj-spin { to { transform: rotate(360deg); } }
                        .pj-loader {
                            width: 18px; height: 18px;
                            border: 2.5px solid rgba(255,255,255,0.3);
                            border-radius: 50%;
                            border-top-color: #fff;
                            animation: pj-spin 0.8s linear infinite;
                            display: inline-block;
                            vertical-align: middle;
                            margin-right: 10px;
                        }
                    `;
                    document.head.appendChild(style);
                }

                // Nonaktifkan tombol untuk prevent double click
                submitBtn.disabled = true;
                
                // Tambahkan class agar terlihat seperti memproses
                submitBtn.classList.add('opacity-70', 'cursor-not-allowed', 'pointer-events-none');
                
                // Jaga ukuran lebar agar layout tidak bergeser/patah saat text diubah
                if (originalWidth > 0 && !submitBtn.style.width) {
                    submitBtn.style.minWidth = originalWidth + 'px';
                }

                // Ubah konten tombol menjadi loading state premium
                submitBtn.innerHTML = `
                    <div class="pj-loader"></div>
                    Memproses...
                `;
            }
        });
    });
});
