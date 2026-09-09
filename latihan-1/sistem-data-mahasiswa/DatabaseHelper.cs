using Microsoft.Data.Sqlite;

namespace SistemDataMahasiswa;

// database layer: menangani koneksi SQLite dan operasi CRUD
static class DatabaseHelper
{
    private const string DbFileName = "mahasiswa.db";

    private static string ConnectionString =>
        $"Data Source={DbFileName}";

    // inisialisasi database: membuat file database dan tabel mahasiswa jika belum ada
    public static void Inisialisasi()
    {
        using var connection = BukaKoneksi();
        using var command = connection.CreateCommand();
        command.CommandText = @"
            CREATE TABLE IF NOT EXISTS mahasiswa (
                nim TEXT PRIMARY KEY,
                nama TEXT NOT NULL,
                prodi TEXT NOT NULL,
                ipk REAL NOT NULL
            );";
        command.ExecuteNonQuery();
    }

    private static SqliteConnection BukaKoneksi()
    {
        var connection = new SqliteConnection(ConnectionString);
        connection.Open();
        return connection;
    }

    // Mengambil semua data mahasiswa
    public static List<Mahasiswa> AmbilSemua()
    {
        var hasil = new List<Mahasiswa>();

        using var connection = BukaKoneksi();
        using var command = connection.CreateCommand();
        command.CommandText = "SELECT nim, nama, prodi, ipk FROM mahasiswa ORDER BY nama;";

        using var reader = command.ExecuteReader();
        while (reader.Read())
        {
            hasil.Add(new Mahasiswa
            {
                NIM = reader.GetString(0),
                Nama = reader.GetString(1),
                Prodi = reader.GetString(2),
                IPK = reader.GetDouble(3)
            });
        }

        return hasil;
    }

    // Menambahkan mahasiswa baru
    public static void Tambah(Mahasiswa mahasiswa)
    {
        using var connection = BukaKoneksi();
        using var command = connection.CreateCommand();
        command.CommandText = @"
            INSERT INTO mahasiswa (nim, nama, prodi, ipk)
            VALUES ($nim, $nama, $prodi, $ipk);";
        command.Parameters.AddWithValue("$nim", mahasiswa.NIM);
        command.Parameters.AddWithValue("$nama", mahasiswa.Nama);
        command.Parameters.AddWithValue("$prodi", mahasiswa.Prodi);
        command.Parameters.AddWithValue("$ipk", mahasiswa.IPK);
        command.ExecuteNonQuery();
    }

    // Mencari mahasiswa berdasarkan NIM (case-insensitive)
    public static Mahasiswa? Cari(string nim)
    {
        using var connection = BukaKoneksi();
        using var command = connection.CreateCommand();
        command.CommandText = @"
            SELECT nim, nama, prodi, ipk FROM mahasiswa
            WHERE nim = $nim COLLATE NOCASE
            LIMIT 1;";
        command.Parameters.AddWithValue("$nim", nim);

        using var reader = command.ExecuteReader();
        if (reader.Read())
        {
            return new Mahasiswa
            {
                NIM = reader.GetString(0),
                Nama = reader.GetString(1),
                Prodi = reader.GetString(2),
                IPK = reader.GetDouble(3)
            };
        }

        return null;
    }

    // Menghapus mahasiswa berdasarkan NIM
    public static bool Hapus(string nim)
    {
        using var connection = BukaKoneksi();
        using var command = connection.CreateCommand();
        command.CommandText = "DELETE FROM mahasiswa WHERE nim = $nim COLLATE NOCASE;";
        command.Parameters.AddWithValue("$nim", nim);
        return command.ExecuteNonQuery() > 0;
    }
}
