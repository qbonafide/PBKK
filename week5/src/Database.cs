using System.Collections.Generic;
using MySqlConnector;

namespace StudentRegistrationApp
{
    public static class Database
    {
        private const string ConnectionString =
            "Server=localhost;Port=3306;Database=student_db;Uid=root;Pwd=;";

        public static List<Mahasiswa> GetAll(string keyword = "")
        {
            var list = new List<Mahasiswa>();

            using var conn = new MySqlConnection(ConnectionString);
            conn.Open();

            const string sql =
                @"SELECT * FROM mahasiswa
                  WHERE nim LIKE @k OR nama LIKE @k OR prodi LIKE @k
                  ORDER BY id DESC";

            using var cmd = new MySqlCommand(sql, conn);
            cmd.Parameters.AddWithValue("@k", "%" + keyword + "%");

            using var r = cmd.ExecuteReader();
            while (r.Read())
            {
                list.Add(new Mahasiswa
                {
                    Id = r.GetInt32("id"),
                    Nim = r.GetString("nim"),
                    Nama = r.GetString("nama"),
                    Prodi = r.GetString("prodi"),
                    JenisKelamin = r.GetString("jenis_kelamin"),
                    TanggalLahir = r.GetDateTime("tanggal_lahir"),
                    Alamat = r.GetString("alamat"),
                    NoTelepon = r.GetString("no_telepon")
                });
            }
            return list;
        }

        public static void Insert(Mahasiswa m)
        {
            using var conn = new MySqlConnection(ConnectionString);
            conn.Open();

            const string sql =
                @"INSERT INTO mahasiswa
                  (nim, nama, prodi, jenis_kelamin, tanggal_lahir, alamat, no_telepon)
                  VALUES (@nim, @nama, @prodi, @jk, @tgl, @alamat, @telp)";

            using var cmd = new MySqlCommand(sql, conn);
            AddParams(cmd, m);
            cmd.ExecuteNonQuery();
        }

        public static void Update(Mahasiswa m)
        {
            using var conn = new MySqlConnection(ConnectionString);
            conn.Open();

            const string sql =
                @"UPDATE mahasiswa SET
                    nim=@nim, nama=@nama, prodi=@prodi, jenis_kelamin=@jk,
                    tanggal_lahir=@tgl, alamat=@alamat, no_telepon=@telp
                  WHERE id=@id";

            using var cmd = new MySqlCommand(sql, conn);
            AddParams(cmd, m);
            cmd.Parameters.AddWithValue("@id", m.Id);
            cmd.ExecuteNonQuery();
        }

        public static void Delete(int id)
        {
            using var conn = new MySqlConnection(ConnectionString);
            conn.Open();

            using var cmd = new MySqlCommand("DELETE FROM mahasiswa WHERE id=@id", conn);
            cmd.Parameters.AddWithValue("@id", id);
            cmd.ExecuteNonQuery();
        }

        // Cek NIM sudah dipakai atau belum (excludeId dipakai saat edit)
        public static bool NimExists(string nim, int excludeId = 0)
        {
            using var conn = new MySqlConnection(ConnectionString);
            conn.Open();

            using var cmd = new MySqlCommand(
                "SELECT COUNT(*) FROM mahasiswa WHERE nim=@nim AND id<>@id", conn);
            cmd.Parameters.AddWithValue("@nim", nim);
            cmd.Parameters.AddWithValue("@id", excludeId);

            return System.Convert.ToInt32(cmd.ExecuteScalar()) > 0;
        }

        private static void AddParams(MySqlCommand cmd, Mahasiswa m)
        {
            cmd.Parameters.AddWithValue("@nim", m.Nim);
            cmd.Parameters.AddWithValue("@nama", m.Nama);
            cmd.Parameters.AddWithValue("@prodi", m.Prodi);
            cmd.Parameters.AddWithValue("@jk", m.JenisKelamin);
            cmd.Parameters.AddWithValue("@tgl", m.TanggalLahir);
            cmd.Parameters.AddWithValue("@alamat", m.Alamat);
            cmd.Parameters.AddWithValue("@telp", m.NoTelepon);
        }
    }
}
