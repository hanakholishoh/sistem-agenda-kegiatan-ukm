<?php
/**
 * MINI APPLICATION OOP: SISTEM PENGELOLAAN AGENDA KEGIATAN UKM KREATIF
 * Konsep: interface, abstraction, inheritance, overriding, polymorphism,
 * composition, encapsulation, dan validasi.
 * Untuk browser: jalankan `php -S localhost:8000`, lalu buka localhost:8000.
 */

echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Sistem Agenda UKM Kreatif</title>";
echo "<style>body{font-family:Consolas,monospace;background:#f4f6f8;color:#20252b;margin:0;padding:24px}main{max-width:1000px;margin:auto;background:#fff;padding:24px;border-radius:12px;box-shadow:0 3px 14px #0001}pre{white-space:pre-wrap;overflow-wrap:anywhere;font-size:14px;line-height:1.55}h1{font-size:22px}.note{font-family:Arial,sans-serif;color:#555}</style></head><body><main>";
echo "<h1>SISTEM PENGELOLAAN AGENDA KEGIATAN UKM KREATIF</h1><pre>";

// INTERFACE: kontrak untuk objek agenda yang dapat dikelola.
interface BisaDikelola
{
    public function getIdAgenda(): string;
    public function getNamaKegiatan(): string;
    public function getStatus(): string;
    public function getJenisAgenda(): string;
    public function tampilkanInfo(): string;
    public function tandaiSelesai(): void;
    public function batalkanAgenda(string $alasan): void;
}

// ABSTRACT CLASS: kelas dasar untuk semua jenis agenda.
abstract class AgendaKegiatan implements BisaDikelola
{
    private string $idAgenda;
    private string $namaKegiatan;
    private string $tanggal;
    private string $lokasi;
    private string $status;
    private string $alasanPembatalan = '';

    public function __construct(string $idAgenda, string $namaKegiatan, string $tanggal, string $lokasi)
    {
        $this->validasiTeks($idAgenda, 'ID agenda');
        $this->validasiTeks($namaKegiatan, 'Nama kegiatan');
        $this->validasiTanggal($tanggal);
        $this->validasiTeks($lokasi, 'Lokasi');
        $this->idAgenda = trim($idAgenda);
        $this->namaKegiatan = trim($namaKegiatan);
        $this->tanggal = trim($tanggal);
        $this->lokasi = trim($lokasi);
        $this->status = 'Direncanakan';
    }

    protected function validasiTeks(string $nilai, string $field): void
    {
        if (trim($nilai) === '') {
            throw new InvalidArgumentException("$field tidak boleh kosong.");
        }
    }

    private function validasiTanggal(string $tanggal): void
    {
        $tanggal = trim($tanggal);
        $objTanggal = DateTime::createFromFormat('!Y-m-d', $tanggal);
        $error = DateTime::getLastErrors();
        if ($objTanggal === false
            || ($error !== false && ($error['warning_count'] > 0 || $error['error_count'] > 0))
            || $objTanggal->format('Y-m-d') !== $tanggal) {
            throw new InvalidArgumentException('Tanggal harus valid dengan format YYYY-MM-DD.');
        }
    }

    public function getIdAgenda(): string { return $this->idAgenda; }
    public function getNamaKegiatan(): string { return $this->namaKegiatan; }
    public function getTanggal(): string { return $this->tanggal; }
    public function getLokasi(): string { return $this->lokasi; }
    public function getStatus(): string { return $this->status; }
    public function getAlasanPembatalan(): string { return $this->alasanPembatalan; }

    abstract public function getJenisAgenda(): string;
    abstract protected function getDetailTambahan(): string;

    public function tampilkanInfo(): string
    {
        $hasil = "ID Agenda : {$this->idAgenda}\n"
            . "Nama      : {$this->namaKegiatan}\n"
            . "Jenis     : {$this->getJenisAgenda()}\n"
            . "Tanggal   : {$this->tanggal}\n"
            . "Lokasi    : {$this->lokasi}\n"
            . "Status    : {$this->status}\n"
            . $this->getDetailTambahan();
        if ($this->status === 'Dibatalkan') {
            $hasil .= "Alasan    : {$this->alasanPembatalan}\n";
        }
        return $hasil;
    }

    public function tandaiSelesai(): void
    {
        if ($this->status === 'Dibatalkan') {
            throw new LogicException('Agenda yang dibatalkan tidak dapat ditandai selesai.');
        }
        $this->status = 'Selesai';
    }

    public function batalkanAgenda(string $alasan): void
    {
        $this->validasiTeks($alasan, 'Alasan pembatalan');
        if ($this->status === 'Selesai') {
            throw new LogicException('Agenda yang sudah selesai tidak dapat dibatalkan.');
        }
        $this->status = 'Dibatalkan';
        $this->alasanPembatalan = trim($alasan);
    }
}

// INHERITANCE + OVERRIDING: agenda workshop.
class AgendaWorkshop extends AgendaKegiatan
{
    private string $materi;
    private string $pemateri;

    public function __construct(string $id, string $nama, string $tanggal, string $lokasi, string $materi, string $pemateri)
    {
        parent::__construct($id, $nama, $tanggal, $lokasi);
        $this->validasiTeks($materi, 'Materi workshop');
        $this->validasiTeks($pemateri, 'Pemateri');
        $this->materi = trim($materi);
        $this->pemateri = trim($pemateri);
    }

    public function getJenisAgenda(): string { return 'Workshop'; }

    protected function getDetailTambahan(): string
    {
        return "Materi    : {$this->materi}\nPemateri  : {$this->pemateri}\n";
    }
}

// INHERITANCE + OVERRIDING: agenda pameran.
class AgendaPameran extends AgendaKegiatan
{
    private string $tema;
    private int $jumlahKarya;

    public function __construct(string $id, string $nama, string $tanggal, string $lokasi, string $tema, int $jumlahKarya)
    {
        parent::__construct($id, $nama, $tanggal, $lokasi);
        $this->validasiTeks($tema, 'Tema pameran');
        if ($jumlahKarya < 1) {
            throw new InvalidArgumentException('Jumlah karya minimal 1.');
        }
        $this->tema = trim($tema);
        $this->jumlahKarya = $jumlahKarya;
    }

    public function getJenisAgenda(): string { return 'Pameran Karya'; }

    protected function getDetailTambahan(): string
    {
        return "Tema      : {$this->tema}\nJumlah karya: {$this->jumlahKarya}\n";
    }
}

// INHERITANCE + OVERRIDING: agenda rapat kepanitiaan.
class AgendaKepanitiaan extends AgendaKegiatan
{
    private string $divisi;
    private string $pembahasan;

    public function __construct(string $id, string $nama, string $tanggal, string $lokasi, string $divisi, string $pembahasan)
    {
        parent::__construct($id, $nama, $tanggal, $lokasi);
        $this->validasiTeks($divisi, 'Divisi');
        $this->validasiTeks($pembahasan, 'Pembahasan');
        $this->divisi = trim($divisi);
        $this->pembahasan = trim($pembahasan);
    }

    public function getJenisAgenda(): string { return 'Kepanitiaan'; }

    protected function getDetailTambahan(): string
    {
        return "Divisi    : {$this->divisi}\nPembahasan: {$this->pembahasan}\n";
    }
}

// COMPOSITION: daftar menyimpan beberapa objek agenda.
class DaftarAgenda
{
    /** @var AgendaKegiatan[] */
    private array $daftar = [];

    public function tambahAgenda(AgendaKegiatan $agenda): void
    {
        foreach ($this->daftar as $item) {
            if ($item->getIdAgenda() === $agenda->getIdAgenda()) {
                throw new InvalidArgumentException('ID agenda sudah digunakan.');
            }
        }
        $this->daftar[] = $agenda;
    }

    public function cariAgenda(string $id): ?AgendaKegiatan
    {
        foreach ($this->daftar as $agenda) {
            if ($agenda->getIdAgenda() === trim($id)) {
                return $agenda;
            }
        }
        return null;
    }

    public function tampilkanSemua(): void
    {
        echo "[ DAFTAR AGENDA ]\n";
        foreach ($this->daftar as $agenda) {
            // POLYMORPHISM: satu pola pemanggilan untuk jenis agenda berbeda.
            echo $agenda->getIdAgenda() . ' | ' . $agenda->getNamaKegiatan()
                . ' | ' . $agenda->getJenisAgenda() . ' | ' . $agenda->getTanggal()
                . ' | Status: ' . $agenda->getStatus() . "\n";
        }
        echo "Total agenda: " . count($this->daftar) . "\n";
    }

    public function getJumlahAgenda(): int { return count($this->daftar); }
}

// Fungsi polymorphism: menerima berbagai subclass AgendaKegiatan.
function tampilkanAgenda(AgendaKegiatan $agenda): void
{
    echo $agenda->tampilkanInfo() . "\n";
}

function ujiValidasi(string $judul, callable $aksi): void
{
    echo "\n$judul\n";
    try {
        $aksi();
        echo "Hasil: BERHASIL (tidak ada error)\n";
    } catch (Throwable $error) {
        echo "Hasil: ERROR TERDETEKSI\nPesan: " . $error->getMessage() . "\n";
    }
}

// PROGRAM UTAMA
try {
    echo "============================================================\n";
    echo " CONTOH DATA AGENDA KEGIATAN UKM KREATIF\n";
    echo "============================================================\n";

    $daftar = new DaftarAgenda();
    $workshop = new AgendaWorkshop('UKM-001', 'Workshop Dasar Ilustrasi Digital', '2026-11-12',
        'Studio Kreatif Kampus', 'Teknik ilustrasi digital pemula', 'Kak Nara');
    $pameran = new AgendaPameran('UKM-002', 'Pameran Karya Anggota', '2026-11-20',
        'Galeri Kampus', 'Ruang dan Cerita', 18);
    $rapat = new AgendaKepanitiaan('UKM-003', 'Rapat Persiapan Pameran', '2026-11-08',
        'Ruang Diskusi UKM', 'Acara dan Publikasi', 'Pembagian tugas dan linimasa');

    $daftar->tambahAgenda($workshop);
    $daftar->tambahAgenda($pameran);
    $daftar->tambahAgenda($rapat);

    echo "\n";
    $daftar->tampilkanSemua();

    echo "\n[ DETAIL AGENDA ]\n";
    foreach ([$workshop, $pameran, $rapat] as $agenda) {
        tampilkanAgenda($agenda);
        echo "------------------------------------------------------------\n";
    }

    echo "\n[ PERUBAHAN STATUS ]\n";
    $workshop->tandaiSelesai();
    echo $workshop->getNamaKegiatan() . " => " . $workshop->getStatus() . "\n";
    $rapat->batalkanAgenda('Bentrok dengan jadwal kegiatan lain');
    echo $rapat->getNamaKegiatan() . " => " . $rapat->getStatus() . "\n";
    echo "Alasan: " . $rapat->getAlasanPembatalan() . "\n";

    echo "\n[ DAFTAR SETELAH PERUBAHAN STATUS ]\n";
    $daftar->tampilkanSemua();

    echo "\n[ PENGUJIAN VALIDASI ]\n";
    ujiValidasi('1. Menguji nama kegiatan kosong', function (): void {
        new AgendaWorkshop('UKM-004', '   ', '2026-11-25', 'Studio Kampus', 'Materi ilustrasi', 'Kak Nara');
    });
    ujiValidasi('2. Menguji tanggal tidak valid', function (): void {
        new AgendaPameran('UKM-005', 'Pameran Mini', '2026-02-30', 'Galeri Kampus', 'Eksperimen Warna', 5);
    });
    ujiValidasi('3. Menguji jumlah karya kurang dari satu', function (): void {
        new AgendaPameran('UKM-006', 'Pameran Mini', '2026-11-25', 'Galeri Kampus', 'Eksperimen Warna', 0);
    });
    ujiValidasi('4. Menguji alasan pembatalan kosong', function () use ($pameran): void {
        $pameran->batalkanAgenda('   ');
    });
    ujiValidasi('5. Menguji ID agenda duplikat', function () use ($daftar): void {
        $daftar->tambahAgenda(new AgendaWorkshop('UKM-001', 'Agenda Duplikat', '2026-11-30',
            'Studio Kampus', 'Materi baru', 'Kak Nara'));
    });

    echo "\n============================================================\nPROGRAM SELESAI DIJALANKAN\n============================================================\n";
} catch (Throwable $error) {
    echo "\nKESALAHAN PROGRAM: " . $error->getMessage() . "\n";
}
echo "</pre><p class='note'>Data pada contoh ini bersifat simulasi dan tidak disimpan ke database.</p></main></body></html>";
