# Jales ChatBot

Aplikasi web chatbot cerdas yang menggunakan Google Gemini AI API dengan sistem autentikasi multi-role dan manajemen pengguna yang lengkap.

## Fitur Utama

### Chatbot AI
- **Integrasi Google Gemini AI**: Menggunakan model `gemini-1.5-flash-latest` untuk respons yang cerdas dan kontekstual
- **Chat Session Management**: Setiap percakapan tersimpan dalam session yang dapat dilanjutkan
- **Chat History**: Riwayat percakapan tersimpan dan dapat diakses kembali
- **Real-time Typing Indicator**: Indikator mengetik saat AI sedang memproses
- **Responsive Design**: Interface yang responsif untuk desktop dan mobile

### 👥 Sistem Multi-Role
#### Role Admin
- **Dashboard Analytics**: Statistik lengkap pengguna dan aktivitas chat
- **User Management**: Kelola semua pengguna sistem
- **Ban/Unban Users**: Blokir atau aktifkan kembali pengguna
- **Role Management**: Ubah role pengguna (admin/user)
- **Delete Users**: Hapus pengguna dan semua data chatnya
- **Chat Monitoring**: Lihat dan hapus riwayat chat pengguna

#### Role User
- **Personal Chatbot**: Interface chat pribadi dengan AI
- **Session Management**: Buat, edit, dan hapus session chat
- **Chat Title Editing**: Ubah judul percakapan
- **History Sidebar**: Akses cepat ke percakapan sebelumnya

### 🔐 Sistem Keamanan
- **Authentication**: Login/register dengan validasi keamanan
- **Role-based Access Control**: Pembatasan akses berdasarkan role
- **Banned User Check**: Middleware untuk mengecek status banned
- **CSRF Protection**: Perlindungan dari serangan CSRF
- **Input Validation**: Validasi input untuk keamanan data

### 📱 User Experience
- **Dark Theme**: Desain gelap yang modern dan elegan
- **Mobile Responsive**: Optimasi untuk semua ukuran layar
- **Smooth Animations**: Animasi yang halus dan engaging
- **Loading States**: Indikator loading yang informatif
- **Error Handling**: Penanganan error yang user-friendly

## 🛠️ Teknologi yang Digunakan

### Backend
- **Laravel 11**: Framework PHP modern
- **MySQL**: Database relational
- **Google Gemini AI API**: AI service untuk chatbot
- **Guzzle HTTP**: HTTP client untuk API calls

### Frontend
- **Blade Templates**: Template engine Laravel
- **Vanilla JavaScript**: Interaktivitas tanpa framework tambahan
- **CSS3**: Styling modern dengan custom properties
- **Font Awesome**: Icon library
- **Chart.js**: Library untuk dashboard charts

### Database
- **Users Table**: Data pengguna dengan role dan status
- **Chat Histories Table**: Riwayat percakapan dengan session management
  
## 🎯 Penggunaan

### Akun Default
Setelah seeding, tersedia akun admin default:
- **Email**: admin@admin.com
- **Password**: admin123

### Alur Penggunaan

1. **Registrasi/Login**: Pengguna baru registrasi atau login dengan akun existing
2. **Role Assignment**: Admin dapat mengubah role pengguna
3. **Chat dengan AI**: User dapat memulai percakapan dengan AI assistant
4. **Session Management**: Percakapan tersimpan dalam session yang dapat dilanjutkan
5. **Admin Monitoring**: Admin dapat memantau aktivitas dan mengelola pengguna

## 📊 Screenshot

### 1. Halaman Login
![Login Page](https://github.com/user-attachments/assets/a51b6763-3e40-4707-a66b-7d77fa6ee658)
*Interface login yang elegant dengan gradient background dan form yang user-friendly*

### 2. Dashboard Admin
![Admin Dashboard](https://github.com/user-attachments/assets/c6243424-a672-49b2-be1b-d36234267fd9)
*Dashboard admin dengan statistik real-time, chart pertumbuhan user, dan daftar user terbaru*

### 3. Kelola User (Admin)
![User Management](https://github.com/user-attachments/assets/289f8f22-f782-4b6f-bc7e-2d92c129d676)
*Panel manajemen user dengan fitur filter, ban/unban, role management, dan aksi pengguna*

### 4. Interface Chatbot (User)
![Chatbot Interface](https://github.com/user-attachments/assets/5ccb254a-8932-4819-a83b-2629cf8aa189)
*Interface chat yang modern dengan sidebar history, typing indicator, dan design yang responsive*

## 🔒 Keamanan

### Implementasi Security
- **Authentication**: Laravel's built-in authentication
- **Authorization**: Role-based access control dengan middleware
- **Input Validation**: Validasi server-side untuk semua input
- **CSRF Protection**: Token CSRF untuk form submission
- **SQL Injection Prevention**: Eloquent ORM untuk database queries
- **XSS Prevention**: Blade template escaping

### Best Practices
- Password hashing dengan bcrypt
- Session security dengan proper configuration
- API key protection dengan environment variables
- Rate limiting untuk API calls
- Proper error messages tanpa information disclosure

## 📄 Lisensi

Proyek ini menggunakan lisensi MIT. Lihat file `LICENSE` untuk detail lengkap.

## 📞 Support

Untuk pertanyaan :
- **Email**: [jalesfariz22@gmail.com]

---

**Jales ChatBot** - AI-powered conversation platform with advanced user management system.
