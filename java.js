let currentStep = 1;
const totalSteps = 6;

// Progres
function updateProgress() {
    const progress = (currentStep / totalSteps) * 100;
    document.getElementById('progressFill').style.width = progress + '%';
}

// Show current step
function showStep(step) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
    });

    // Show current section
    const currentSection = document.querySelector(`[data-section="${step}"]`);
    if (currentSection) {
        currentSection.classList.add('active');
    }

    // Update step navigation
    document.querySelectorAll('.step-item').forEach((item, index) => {
        item.classList.remove('active', 'completed');
        if (index + 1 === step) {
            item.classList.add('active');
        } else if (index + 1 < step) {
            item.classList.add('completed');
        }
    });

    // Update buttons
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const btnSubmit = document.getElementById('btnSubmit');

    btnPrev.disabled = (step === 1);

    if (step === totalSteps) {
        // Step terakhir - tampilkan tombol submit, sembunyikan next
        btnNext.style.display = 'none';
        btnSubmit.style.display = 'inline-block';
        showReviewData();
    } else {
        // Bukan step terakhir - tampilkan next, sembunyikan submit
        btnNext.style.display = 'inline-block';
        btnSubmit.style.display = 'none';
    }

    updateProgress();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Validate current step
function validateStep(step) {
    const section = document.querySelector(`[data-section="${step}"]`);
    const requiredFields = section.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (field.type === 'radio') {
            const radioGroup = section.querySelectorAll(`[name="${field.name}"]`);
            const isChecked = Array.from(radioGroup).some(radio => radio.checked);
            if (!isChecked) {
                isValid = false;
            }
        } else if (field.type === 'checkbox') {
            if (!field.checked) {
                isValid = false;
            }
        } else if (field.type === 'file') {
            if (field.hasAttribute('required') && !field.files.length) {
                isValid = false;
            }
        } else if (!field.value.trim()) {
            isValid = false;
        }
    });

    // Special validations
    if (step === 1) {
        const nik = document.getElementById('nik').value;
        if (!validateNIK(nik)) {
            showError('nik', true);
            isValid = false;
        }

        const email = document.getElementById('email').value;
        if (!validateEmail(email)) {
            showError('email', true);
            isValid = false;
        }

        const noTelepon = document.getElementById('noTelepon').value;
        if (!validatePhone(noTelepon)) {
            showError('noTelepon', true);
            isValid = false;
        }
    }

    if (step === 3) {
        const noTeleponOrtu = document.getElementById('noTeleponOrtu').value;
        if (!validatePhone(noTeleponOrtu)) {
            showError('noTeleponOrtu', true);
            isValid = false;
        }
    }

    return isValid;
}

// Tampil data review
function showReviewData() {
    const form = document.getElementById('registrationForm');
    const formData = new FormData(form);
    let html = '<h3 style="color: #667eea; margin-bottom: 15px;">📋 Review Data Pendaftaran</h3>';

    const sections = [
        { title: 'Data Pribadi', fields: ['namaLengkap', 'tempatLahir', 'tanggalLahir', 'jenisKelamin', 'agama', 'alamat', 'noTelepon', 'email', 'nik'] },
        { title: 'Data Pendidikan', fields: ['asalSekolah', 'jurusanSekolah', 'tahunLulus', 'noIjazah', 'nilaiRata'] },
        { title: 'Data Orang Tua', fields: ['namaAyah', 'namaIbu', 'pekerjaanAyah', 'pekerjaanIbu', 'pendidikanAyah', 'pendidikanIbu', 'alamatOrtu', 'noTeleponOrtu', 'penghasilan', 'namaWali'] },
        { title: 'Program Studi', fields: ['prodi1', 'prodi2', 'jalurPendaftaran'] },
        { title: 'Data Pendukung', fields: ['prestasi', 'organisasi'] }
    ];

    sections.forEach(section => {
        html += `<div style="margin-bottom: 20px;">`;
        html += `<h4 style="color: #333; margin-bottom: 10px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">${section.title}</h4>`;
        section.fields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                const label = document.querySelector(`label[for="${fieldName}"]`)?.textContent.replace('*', '').trim() || fieldName;
                const value = formData.get(fieldName);
                if (value && value.trim()) {
                    html += `<p style="margin: 8px 0;"><strong>${label}:</strong> ${value}</p>`;
                }
            }
        });
        html += `</div>`;
    });

    // Show uploaded files
    const files = ['pasFoto', 'uploadIjazah', 'uploadRapor', 'suratSehat'];
    let hasFiles = false;
    let filesHtml = '';
    
    files.forEach(fileName => {
        const fileInput = document.getElementById(fileName);
        if (fileInput && fileInput.files.length > 0) {
            if (!hasFiles) {
                filesHtml = '<div style="margin-bottom: 20px;"><h4 style="color: #333; margin-bottom: 10px; border-bottom: 2px solid #667eea; padding-bottom: 5px;">📎 File yang Diupload</h4>';
                hasFiles = true;
            }
            const label = document.querySelector(`label[for="${fileName}"]`)?.textContent.replace('*', '').trim() || fileName;
            filesHtml += `<p style="margin: 8px 0;"><strong>${label}:</strong> ${fileInput.files[0].name}</p>`;
        }
    });
    
    if (hasFiles) {
        filesHtml += '</div>';
        html += filesHtml;
    }

    document.getElementById('reviewData').innerHTML = html;
}

// Next button
document.getElementById('btnNext').addEventListener('click', function () {
    if (validateStep(currentStep)) {
        currentStep++;
        showStep(currentStep);
    } else {
        Swal.fire({
            icon: "error",
            title: "Data Belum Lengkap",
            text: "Mohon lengkapi semua field yang wajib diisi!"
        });
    }
});

// Previous button
document.getElementById('btnPrev').addEventListener('click', function () {
    currentStep--;
    showStep(currentStep);
});

// Step navigation click
document.querySelectorAll('.step-item').forEach(item => {
    item.addEventListener('click', function () {
        const targetStep = parseInt(this.dataset.step);

        // Validate all previous steps
        let canNavigate = true;
        for (let i = 1; i < targetStep; i++) {
            if (!validateStep(i)) {
                canNavigate = false;
                break;
            }
        }

        if (canNavigate || targetStep < currentStep) {
            currentStep = targetStep;
            showStep(currentStep);
        } else {
            Swal.fire({
                icon: "error",
                title: "Tidak Bisa Lanjut",
                text: "Harap lengkapi step sebelumnya terlebih dahulu!"
            });
        }
    });
});

// Validasi NIK
function validateNIK(nik) {
    return /^\d{16}$/.test(nik);
}

// Validasi email
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Validasi nomor telepon
function validatePhone(phone) {
    return /^08\d{8,11}$/.test(phone);
}

// Show error
function showError(fieldId, show = true) {
    const errorElement = document.getElementById(`error-${fieldId}`);
    const inputElement = document.getElementById(fieldId);

    if (errorElement && inputElement) {
        if (show) {
            errorElement.style.display = 'block';
            inputElement.classList.add('input-error');
        } else {
            errorElement.style.display = 'none';
            inputElement.classList.remove('input-error');
        }
    }
}

// Real-time validation
document.getElementById('nik').addEventListener('input', function () {
    if (this.value.length > 0) {
        showError('nik', !validateNIK(this.value));
    }
});

document.getElementById('email').addEventListener('input', function () {
    if (this.value.length > 0) {
        showError('email', !validateEmail(this.value));
    }
});

document.getElementById('noTelepon').addEventListener('input', function () {
    if (this.value.length > 0) {
        showError('noTelepon', !validatePhone(this.value));
    }
});

document.getElementById('noTeleponOrtu').addEventListener('input', function () {
    if (this.value.length > 0) {
        showError('noTeleponOrtu', !validatePhone(this.value));
    }
});


document.getElementById('registrationForm').addEventListener('submit', function (e) {
    e.preventDefault();
    
    console.log('🚀 Form submit started...');
    
    // Validasi checkbox persetujuan
    const persetujuan = document.getElementById('persetujuan');
    if (!persetujuan.checked) {
        Swal.fire({
            icon: 'warning',
            title: 'Persetujuan Diperlukan',
            text: 'Anda harus menyetujui pernyataan terlebih dahulu!'
        });
        return;
    }

    // Tampilkan loading
    Swal.fire({
        title: '⏳ Menyimpan Data...',
        text: 'Mohon tunggu, data sedang diproses',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Ambil form data
    const formData = new FormData(this);
    
    // Kirim data menggunakan Fetch API
    fetch('save_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response:', data);
        
        if (data.success) {
            // Tampilkan notifikasi sukses dengan nomor registrasi
            Swal.fire({
                icon: 'success',
                title: '✅ Pendaftaran Berhasil!',
                html: `
                    <div style="text-align: center; padding: 20px;">
                        <p style="font-size: 16px; margin-bottom: 15px;">
                            Selamat! Data Anda telah berhasil disimpan.
                        </p>
                        <div style="background: #e3f2fd; padding: 20px; border-radius: 10px; margin: 20px 0;">
                            <p style="font-size: 14px; color: #666; margin-bottom: 10px;">
                                Nomor Registrasi Anda:
                            </p>
                            <h2 style="font-size: 32px; font-weight: bold; color: #667eea; letter-spacing: 2px; margin: 10px 0;">
                                ${data.nomor_registrasi}
                            </h2>
                        </div>
                        <p style="font-size: 14px; color: #666; margin-top: 15px;">
                            📝 Harap simpan nomor registrasi ini untuk keperluan tracking status pendaftaran Anda.
                        </p>
                        <p style="font-size: 13px; color: #999; margin-top: 10px;">
                            ✉️ Email konfirmasi telah dikirim ke <strong>${data.email}</strong>
                        </p>
                    </div>
                `,
                confirmButtonText: '🏠 Kembali ke Halaman Utama',
                confirmButtonColor: '#667eea',
                allowOutsideClick: false
            }).then(() => {
                // Reset form dan kembali ke step 1
                document.getElementById('registrationForm').reset();
                currentStep = 1;
                showStep(1);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        } else {
            // Tampilkan error
            Swal.fire({
                icon: 'error',
                title: '❌ Pendaftaran Gagal',
                text: data.message || 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '❌ Terjadi Kesalahan',
            text: 'Tidak dapat menghubungi server. Silakan periksa koneksi internet Anda.',
            confirmButtonText: 'Coba Lagi'
        });
    });
});

// Initialize
console.log('✅ JavaScript loaded successfully!');
updateProgress();