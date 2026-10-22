# Sistem Pengelolaan Agenda Kegiatan UKM Kreatif

Mini aplikasi berbasis PHP untuk mengelola agenda kegiatan UKM, seperti workshop kreatif, pameran karya, dan rapat kepanitiaan. Proyek ini dibuat untuk menerapkan konsep **Pemrograman Berorientasi Objek (OOP)**.

## Fitur
- Menampilkan daftar dan detail agenda kegiatan.
- Mengubah status agenda menjadi selesai.
- Membatalkan agenda beserta alasannya.
- Memvalidasi data agenda dan mencegah ID duplikat.
- Menguji validasi melalui beberapa skenario.

## Konsep OOP
- **Interface:** `BisaDikelola`
- **Abstraction:** `AgendaKegiatan`
- **Inheritance:** `AgendaWorkshop`, `AgendaPameran`, dan `AgendaKepanitiaan`
- **Overriding:** Implementasi metode khusus pada setiap jenis agenda.
- **Polymorphism:** Menampilkan objek agenda melalui metode yang sama.
- **Composition:** `DaftarAgenda` mengelola kumpulan objek agenda.
- **Encapsulation:** Penggunaan properti `private`.

## Teknologi
- PHP 8.0+
- HTML dan CSS sederhana



## Catatan
Aplikasi menggunakan data simulasi yang dibuat langsung di dalam kode. Data belum disimpan ke database.
